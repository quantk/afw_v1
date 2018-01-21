<?php
/**
 * Created by PhpStorm.
 * User: quantick
 * Date: 21.01.18
 */

namespace Artifly\Core\Component\ORM\Exception;


class EntityFieldNotFound extends ORMException
{
    protected $message = 'Entity Field not found';
}