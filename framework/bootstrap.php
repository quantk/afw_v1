<?php

require_once('../vendor/autoload.php');
error_reporting(E_ALL & ~E_NOTICE);
use Artifly\Core\Application;

try {
    $app = new Application();
    $router = require_once('../src/routes.php');
    $app->run($router);
} catch (\Throwable $e) {
    echo $e;
}

