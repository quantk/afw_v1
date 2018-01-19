<?php

use Artifly\Core\Route;
use Artifly\Core\Router;
use Artifly\Core\TemplateEngine;

$router = new Router();
$router
    ->addRoute(
        '/closure/{name}',
        function(TemplateEngine $templateEngine, $name) {

            return $templateEngine->render('index_closure.html', ['user' => $name]);
        }
    )
    ->addRoute(
        '/',
        'DefaultController@indexAction',
        [Route::POST_METHOD, Route::GET_METHOD, Route::PUT_METHOD, Route::DELETE_METHOD]
    )
;

return $router;