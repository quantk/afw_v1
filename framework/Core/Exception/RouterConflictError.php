<?php
/**
 * Created by PhpStorm.
 * User: quantick
 * Date: 18.01.18
 * Time: 22:51
 */

namespace Artifly\Core\Exception;


class RouterConflictError extends RouterError
{
    protected $message = 'Router conflict';
}