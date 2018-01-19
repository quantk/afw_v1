<?php
/**
 * Created by PhpStorm.
 * User: quantick
 * Date: 18.01.18
 */

namespace Artifly\Core\Component\Router\Exception;


class RouterConflictError extends RouterError
{
    protected $message = 'Router conflict';
}