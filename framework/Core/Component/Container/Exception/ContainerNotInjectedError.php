<?php
/**
 * Created by PhpStorm.
 * User: quantick
 * Date: 19.01.18
 */

namespace Artifly\Core\Component\Container\Exception;


use Artifly\Core\Exception\Error;

class ContainerNotInjectedError extends Error
{
    protected $message = 'Container not injected!';
}