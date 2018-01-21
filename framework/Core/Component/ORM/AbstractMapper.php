<?php
/**
 * Created by PhpStorm.
 * User: quantick
 * Date: 21.01.18
 */

namespace Artifly\Core\Component\ORM;


class AbstractMapper
{
    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var string
     */
    protected $modelClass;

    /**
     * AbstractMapper constructor.
     *
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param $id
     *
     * @return null|object
     */
    public function find($id)
    {
        return $this->entityManager->find($this->modelClass, $id);
    }


}