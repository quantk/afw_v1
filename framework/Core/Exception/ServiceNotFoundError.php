<?php
/**
 * Created by PhpStorm.
 * User: quantick
 * Date: 19.01.18
 */

namespace Artifly\Core\Exception;


use Psr\Container\NotFoundExceptionInterface;

class ServiceNotFoundError extends Error implements NotFoundExceptionInterface
{
    protected $message = 'Service not found in container';
}