<?php
/**
 * Created by PhpStorm.
 * User: quantick
 * Date: 20.01.18
 */

namespace Artifly\Core\Component\ORM;

use Artifly\Core\Component\ORM\Exception\EntityFieldNotFound;
use Artifly\Core\Component\ORM\Exception\ORMException;


/**
 * Class EntityManager
 *
 * @package Artifly\Core\Component\ORM
 */
class EntityManager
{
//region SECTION: Fields
    /**
     * @var DBConnector
     */
    protected $connection;
//endregion Fields


//region SECTION: Constructor
    /**
     * ActiveRecord constructor.
     *
     * @param DBConnector $connector
     */
    public function __construct(DBConnector $connector)
    {
        $this->connection = $connector;
    }
//endregion Constructor

//region SECTION: Public
    /**
     * @param AbstractEntity $entity
     */
    public function save(AbstractEntity $entity)
    {
        if ($entity->isSaved()) {
            $this->updateAction($entity);
        } else {
            $this->insertAction($entity);
        }
    }

    /**
     * @param $entityClass
     * @param $id
     *
     * @return null|object
     */
    public function find($entityClass, $id)
    {
        return $this->selectAction($entityClass, ['id' => $id], [], 1);
    }

    /**
     * @param string $entityClass
     * @param array  $criterias
     * @param array  $orderBy
     *
     * @return mixed
     * @throws EntityFieldNotFound
     */
    public function findBy(string $entityClass, array $criterias = [], array $orderBy = [])
    {
        return $this->selectAction($entityClass, $criterias, $orderBy);
    }

    /**
     * @param string $entityClass
     * @param array  $criterias
     * @param array  $orderBy
     *
     * @return mixed
     * @throws EntityFieldNotFound
     */
    public function findOneBy(string $entityClass, array $criterias = [], array $orderBy = [])
    {
        return $this->selectAction($entityClass, $criterias, $orderBy, 1);
    }

    /**
     * @param        $sql
     * @param string $objectClass
     *
     * @return bool|mixed
     */
    public function executeRaw($sql, $objectClass = '')
    {

        $pdo  = $this->connection->getPdo();
        $stmt = $pdo->query($sql, \PDO::FETCH_ASSOC);

        $result = true;
        if ($stmt && $stmt->columnCount() > 0) {
            $result = $this->parseQueryResult($objectClass, $stmt);
        }

        return $result;
    }
//endregion Public

//region SECTION: Private
    /**
     * @param string $entityClass
     * @param array  $criterias
     * @param array  $orderBy
     *
     * @param int    $limit
     *
     * @return mixed
     * @throws EntityFieldNotFound
     */
    private function selectAction(string $entityClass, array $criterias = [], array $orderBy = [], $limit = 0)
    {
        if (!is_int($limit)) {
            throw new ORMException('Limit argument must be integer');
        }
        $tableName = $this->getEntityTableName($entityClass);

        $rTable = new \ReflectionClass($entityClass);
        $where  = '';
        $order  = '';

        foreach ($orderBy as $columnName => $orderType) {
            if (!in_array($orderType, ['ASC', 'DESC'])) {
                throw new ORMException('Wrong order type ' . $orderType);
            }
            if ($order !== '') {
                $order .= ' AND ';
            } else {
                $order = 'ORDER BY ';
            }

            if (!$rTable->hasProperty($columnName)) {
                throw new EntityFieldNotFound();
            }

            $order .= sprintf('%s %s', $columnName, strtoupper($orderType));
        }

        foreach ($criterias as $criteria => $value) {
            if ($where !== '') {
                $where .= ' AND ';
            } else {
                $where = 'WHERE ';
            }
            if (!$rTable->hasProperty($criteria)) {
                throw new EntityFieldNotFound();
            }

            $where .= sprintf('%s=:%s', $criteria, $criteria);
        }

        $pdo = $this->connection->getPdo();

        $limitStr = $limit && is_numeric($limit) ? 'LIMIT '.$limit : '';

        $stmt = $pdo->prepare(
            sprintf(
                "SELECT * FROM `%s` %s %s %s;",
                $tableName,
                $where,
                $order,
                $limitStr
            )
        );
        $this->bindParams($criterias, $stmt);

        $stmt->execute();

        $result = $this->parseQueryResult($entityClass, $stmt, $limit !== 1);

        return $result;
    }

    /**
     * @param AbstractEntity|string $entity
     *
     * @return string
     */
    private function getEntityTableName($entity)
    {
        return strtolower((new \ReflectionClass($entity))->getShortName()).'s';
    }

    /**
     * @param AbstractEntity $entity
     */
    private function insertAction(AbstractEntity $entity)
    {
        $tableName      = $this->getEntityTableName($entity);
        $rTable         = new \ReflectionClass($entity);
        $pdo            = $this->connection->getPdo();
        $propertyNames  = [];
        $propertyValues = [];
        $paramsString   = '';
        foreach ($rTable->getProperties() as $property) {
            if (in_array($property->getName(), $entity->getExcludedFields())) {
                continue;
            }
            $property->setAccessible(true);
            $propertyNames[]                      = sprintf('`%s`', $property->getName());
            $paramsString                         .= sprintf(":%s, ", $property->getName());
            $propertyValues[$property->getName()] = $property->getValue($entity);
        }
        $properties = implode(',', $propertyNames);
        $pdo->beginTransaction();
        $stmt = $pdo->prepare(
            sprintf("INSERT INTO `%s` (%s) VALUES (%s);", $tableName, $properties, rtrim($paramsString, ', '))
        );

        $this->bindParams($propertyValues, $stmt);

        $stmt->execute();
        $idProp = $rTable->getProperty('id');
        $idProp->setAccessible(true);
        $idProp->setValue($entity, $pdo->lastInsertId());
        $pdo->commit();
    }

    /**
     * @param AbstractEntity $entity
     */
    private function updateAction(AbstractEntity $entity)
    {
        $tableName      = $this->getEntityTableName($entity);
        $rTable         = new \ReflectionClass($entity);
        $pdo            = $this->connection->getPdo();
        $propertyValues = [];
        $paramsString   = '';
        foreach ($rTable->getProperties() as $property) {
            if (in_array($property->getName(), $entity->getExcludedFields())) {
                continue;
            }
            $property->setAccessible(true);
            $paramsString                         .= sprintf("%s=:%s, ", $property->getName(), $property->getName());
            $propertyValues[$property->getName()] = $property->getValue($entity);
        }
        $pdo->beginTransaction();
        $stmt = $pdo->prepare(
            sprintf("UPDATE `%s` SET %s WHERE id=%s;", $tableName, rtrim($paramsString, ', '), $entity->getId())
        );

        $this->bindParams($propertyValues, $stmt);

        $stmt->execute();
        $pdo->commit();
    }

    /**
     * @param               $propertyValues
     * @param \PDOStatement $stmt
     *
     * @return mixed
     */
    private function bindParams($propertyValues, \PDOStatement $stmt): void
    {
        foreach ($propertyValues as $propertyName => &$propertyValue) {
            $stmt->bindParam(':'.$propertyName, $propertyValue);
        }
    }

    /**
     * @param               $objectClass
     * @param \PDOStatement $stmt
     *
     * @param bool          $isCollection
     *
     * @return mixed
     */
    private function parseQueryResult($objectClass, $stmt, $isCollection = true)
    {
        if ($isCollection) {
            $result = $objectClass ? $stmt->fetchAll(\PDO::FETCH_CLASS, $objectClass) : $stmt->fetchAll();
        } else {
            if ($objectClass) {
                $stmt->setFetchMode(\PDO::FETCH_CLASS, $objectClass);
            }

            $result = $stmt->fetch();
        }

        return $result;
    }
//endregion Private
}