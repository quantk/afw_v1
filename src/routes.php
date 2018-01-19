<?php

$router = new \Artifly\Core\Router();
$router
    ->addRoute(
        '/closure/{name}',
        function(\Artifly\Core\Container $container, \Artifly\Core\TemplateEngine $templateEngine, $name) {

            return $templateEngine->render('index_closure.html', ['user' => $name]);
        }
    )
    ->addRoute(
        '/',
        'DefaultController@indexAction'
    )
;

return $router;