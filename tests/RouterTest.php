<?php
/**
 * Created by PhpStorm.
 * User: quantick
 * Date: 21.01.18
 */

namespace Tests;


use Artifly\Core\Component\Router\DispatchedRoute;
use Artifly\Core\Component\Router\Route;
use Artifly\Core\Component\Router\Router;
use PHPUnit\Framework\TestCase;

class RouterTest extends TestCase
{
    public function testRouter()
    {
        $router = new Router();
        $router
            ->addRoute(
                '/hello/{name}',
                function($name) {
                    return 'Hello,' . $name;
                }
            )
        ;
        $this->assertInstanceOf(Route::class, $router->getHeadRoute());

        $dispatchedRoute = $router->dispatch('/hello/vasya', 'GET');
        $this->assertInstanceOf(DispatchedRoute::class, $dispatchedRoute);

        switch ($dispatchedRoute->getDispatchType()) {
            case DispatchedRoute::ROUTE_FOUNDED:
                $handlerResult = $dispatchedRoute->getHandler()($dispatchedRoute->getArgs()[0]);
                $this->assertTrue($handlerResult === 'Hello,vasya');
                break;
        }

        $dispatchedRoute = $router->dispatch('/notFoundRoute', 'GET');

        $notFound = false;
        switch ($dispatchedRoute->getDispatchType()) {
            case DispatchedRoute::ROUTE_NOT_FOUNDED:
                $notFound = true;
                break;
        }

        $this->assertTrue($notFound);
    }
}