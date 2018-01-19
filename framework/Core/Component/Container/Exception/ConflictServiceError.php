<?php
/**
 * Created by PhpStorm.
 * User: quantick
 * Date: 19.01.18
 */

namespace Artifly\Core\Component\Container\Exception;


use Artifly\Core\Exception\Error;
use Psr\Container\ContainerExceptionInterface;

class ConflictServiceError extends Error implements ContainerExceptionInterface
{
    protected $message = 'Service id already in use';
}