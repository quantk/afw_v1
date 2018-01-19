<?php
/**
 * Created by PhpStorm.
 * User: quantick
 * Date: 18.01.18
 */

namespace Artifly\Core;

use Artifly\Core\Exception\ControllerResponseError;
use Artifly\Core\Exception\RouterConflictError;
use Artifly\Core\Exception\RouterError;
use Symfony\Component\HttpFoundation\Request;


/**
 * Class Router
 *
 * @package Artifly\Core
 */
class Router
{
//region SECTION: Fields
    /**
     * @var Route[]
     */
    private $routes = [];

    /**
     * @var Route[]
     */
    private $namedRoutes = [];

    /**
     * @var DispatchedRoute
     */
    private $currentRoute = null;
//endregion Fields

//region SECTION: Constructor
    /**
     * Router constructor.
     */
    public function __construct()
    {
        //todo: temp route for 404 page
        $this->addRoute(
            "/404",
            function () {
                return "<h1>Page not found :(</h1>";
            }
        );
    }
//endregion Constructor

//region SECTION: Public

    /**
     * @param        $routePath
     * @param        $handler
     * @param string $method
     * @param string $routeName
     *
     * @return $this
     * @throws RouterConflictError
     */
    public function addRoute($routePath, $handler, $method = Route::GET_METHOD, $routeName = ''): Router
    {
        if (!$handler instanceof \Closure) {
            if (!strstr($handler, '@')) {
                throw new RouterError('Wrong format of route handler');
            }
        }
        $route = new Route($routePath, $handler, $method, $routeName);
        if ($routeName) {
            if (isset($this->namedRoutes[$routeName])) {
                throw new RouterConflictError('Route name already in use');
            }
            $this->namedRoutes[$routeName] = $route;
        }
        if (isset($this->routes[$routePath])) {
            throw new RouterConflictError('Route path already in use');
        }
        $this->routes[$routePath] = $route;

        return $this;
    }

    /**
     * @param Request $request
     *
     * @return DispatchedRoute
     * @throws ControllerResponseError
     */
    public function dispatch(Request $request): DispatchedRoute
    {
        $pathInfo = $request->getPathInfo();

        foreach ($this->routes as $route) {
            $pattern = $this->generateRegexRoute($route);

            if (preg_match($pattern, $pathInfo, $matches)) {
                $dispatchedRoute = $route;
                $args            = array_slice($matches, 1);
                //todo: перенести ActionHandler в другое место. А отсюда возвращать handler в виде строки/closure
                $handlerType = $route->getHandler() instanceof \Closure ? ActionHandler::CLOSURE_TYPE : ActionHandler::CONTROLLER_TYPE;
                $handler     = new ActionHandler($this->getHandler($dispatchedRoute), $handlerType);

                if (!$handler) {
                    throw new RouterError('Something went wrong...');
                } else {
                    $this->currentRoute = new DispatchedRoute(
                        DispatchedRoute::ROUTE_FOUNDED,
                        $handler,
                        $route,
                        $args
                    );

                    return $this->currentRoute;
                }
                break;
            }
        }

        $this->currentRoute = new DispatchedRoute(
            DispatchedRoute::ROUTE_NOT_FOUNDED,
            null
        );

        return $this->currentRoute;
    }
//endregion Public
//region SECTION: Private
    /**
     * @param $route
     *
     * @return string
     */
    private function generateRegexRoute(Route $route): string
    {
        preg_match_all("/\{([\w\d]+)\}/i", $route->getRoutePath(), $arg_names);
        $rgx     = [];
        $rgx[]   = "#^";
        $rgx[]   = preg_replace_callback(
            "/\{[a-zA-Z0-9-_]+\}/",
            function () {
                return "([a-zA-Z0-9-_]+?)";
            },
            $route->getRoutePath()
        );
        $rgx[]   = "/*$#i";
        $pattern = join('', $rgx);

        return $pattern;
    }

    /**
     * @param Route $dispatchedRoute
     *
     * @return mixed
     */
    private function getHandler($dispatchedRoute)
    {
        return $dispatchedRoute->getHandler();
    }
//endregion Private

//region SECTION: Getters/Setters
    /**
     * @return DispatchedRoute
     */
    public function getCurrentRoute(): DispatchedRoute
    {
        return $this->currentRoute;
    }
//endregion Getters/Setters
}