<?php

$loader = require_once('../vendor/autoload.php');

error_reporting(E_ALL & ~E_NOTICE);
use Artifly\Core\Application;

try {
    \Doctrine\Common\Annotations\AnnotationRegistry::registerLoader([$loader, 'loadClass']);
    $app = new Application();
    $router = require_once('../src/routes.php');
    $app->run($router);
} catch (\Throwable $e) {
    echo $e;
}

