<?php
/**
 * Created by PhpStorm.
 * User: quantick
 * Date: 20.01.18
 */

namespace Artifly\Core\Component\ORM;


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
        $tableName = $this->getEntityTableName($entityClass);

        $pdo  = $this->connection->getPdo();
        $stmt = $pdo->prepare(
            sprintf(
                "SELECT * FROM `%s` WHERE id=:id",
                $tableName,
                $id
            )
        );
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $result = $stmt->fetch();
        if ($result === false) {
            return null;
        }

        $rTable = new \ReflectionClass($entityClass);
        $object = $rTable->newInstance();
        foreach ($rTable->getProperties() as $property) {
            $property->setAccessible(true);
            $property->setValue($object, $result[$property->getName()]);
        }

        return $object;
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
            sprintf("INSERT INTO `%s` (%s) VALUES (%s)", $tableName, $properties, rtrim($paramsString, ', '))
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
            sprintf("UPDATE `%s` SET %s WHERE id=%s", $tableName, rtrim($paramsString, ', '), $entity->getId())
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
     * @return mixed
     */
    private function parseQueryResult($objectClass, $stmt)
    {
        if ($objectClass) {
            $result = $stmt->fetchAll(\PDO::FETCH_CLASS, $objectClass);
        } else {
            $result = $stmt->fetchAll();
        }

        return $result;
    }
//endregion Private
}