<?php


use Artifly\Core\Component\Router\Route;
use Artifly\Core\Component\Router\Router;
use Artifly\Core\Component\Template\TemplateEngine;

$router = new Router();
$router
    ->addRoute(
        '/closure/{name}',
        function(TemplateEngine $templateEngine, $name) {
            return $templateEngine->render('index_closure.html', ['user' => $name]);
        }
    )
    ->addRoute(
        '/{userId}',
        'DefaultController@indexAction',
        [Route::POST_METHOD, Route::GET_METHOD, Route::PUT_METHOD, Route::DELETE_METHOD]
    )
;

return $router;