<?php

$router = new \Artifly\Core\Router();
$router
    ->addRoute(
        '/hello/{name}/{lastname}',
        function(\Artifly\Core\Container $container, \Artifly\Core\TemplateEngine $templateEngine, $name, $lastname) {

            return $templateEngine->render('index_closure.html', ['user' => sprintf('%s %s', $name, $lastname)]);
        }
    )
    ->addRoute(
        '/hello/{name}',
        'DefaultController@indexAction'
    )
;

return $router;