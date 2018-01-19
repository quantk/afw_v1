<?php

$router = new \Artifly\Core\Router();
$router
    ->addRoute(
        '/hello/{name}/{lastname}',
        function(\Symfony\Component\HttpFoundation\Request $request, $name, $lastname) {
            $name = ucfirst($name);
            $lastname = ucfirst($lastname);
            return "<h1>Hello, $name $lastname. You are in closure action.</h1>";
        }
    )->addRoute(
        '/hello/{name}',
        'DefaultController@indexAction'
    )
;

return $router;