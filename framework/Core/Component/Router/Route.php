<?php
/**
 * Created by PhpStorm.
 * User: quantick
 * Date: 18.01.18
 */

namespace Artifly\Core\Component\Router;


/**
 * Class Route
 *
 * @package Artifly\Core
 */
class Route
{
//region SECTION: Fields
    public const GET_METHOD = 'GET';
    public const POST_METHOD = 'POST';
    public const PUT_METHOD = 'PUT';
    public const DELETE_METHOD = 'DELETE';

    public const AVAILABLE_METHODS = [
        self::GET_METHOD,
        self::POST_METHOD,
        self::PUT_METHOD,
        self::DELETE_METHOD
    ];

    /**
     * @var string
     */
    private $routeName = '';

    /**
     * @var string
     */
    private $routePath = '';
    /**
     * @var array
     */
    private $methods = [self::GET_METHOD];

    /**
     * @var string|\Closure
     */
    private $handler = '';
//endregion Fields

//region SECTION: Constructor
    /**
     * Route constructor.
     *
     * @param string $routePath
     * @param        $handler
     * @param array  $methods
     * @param string $routeName
     */
    public function __construct($routePath, $handler, $methods = [self::GET_METHOD], $routeName = '')
    {
        $this->routeName = $routeName;
        $this->routePath = $routePath;
        $this->methods    = $methods;
        $this->handler   = $handler;
    }
//endregion Constructor

//region SECTION: Getters/Setters
    /**
     * @return string
     */
    public function getRouteName(): string
    {
        return $this->routeName;
    }

    /**
     * @return string
     */
    public function getRoutePath(): string
    {
        return $this->routePath;
    }

    /**
     * @return array
     */
    public function getMethods(): array
    {
        return $this->methods;
    }

    /**
     * @return \Closure|string
     */
    public function getHandler()
    {
        return $this->handler;
    }

    /**
     * @param string $routeName
     *
     * @return Route
     */
    public function setRouteName($routeName): Route
    {
        $this->routeName = $routeName;

        return $this;
    }

    /**
     * @param string $method
     *
     * @return Route
     */
    public function addMethod($method): Route
    {
        if (!in_array($method, $this->methods) && in_array($method, self::AVAILABLE_METHODS)) {
            $this->methods[] = $method;
        }

        return $this;
    }
//endregion Getters/Setters

}