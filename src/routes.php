<?php

$router = new \Artifly\Core\Router();
$router
    ->addRoute(
        '/hello/{name}/{lastname}',
        function(\Symfony\Component\HttpFoundation\Request $request, $name, $lastname) {
            return "<h1>Hello closure $name $lastname</h1>";
        }
    )->addRoute(
        '/hello/{name}',
        'DefaultController@indexAction'
    )
;

return $router;