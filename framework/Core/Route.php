<?php
/**
 * Created by PhpStorm.
 * User: quantick
 * Date: 18.01.18
 * Time: 22:34
 */

namespace Artifly\Core;


class Route
{
//region SECTION: Fields
    public const GET_METHOD = 'GET';

    public const POST_METHOD = 'POST';

    /**
     * @var string
     */
    private $routeName = '';

    /**
     * @var string
     */
    private $routePath = '';
    /**
     * @var string
     */
    private $method;

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
     * @param string $method
     * @param string $routeName
     */
    public function __construct($routePath, $handler, $method = self::GET_METHOD, $routeName = '')
    {
        $this->routeName = $routeName;
        $this->routePath = $routePath;
        $this->method    = $method;
        $this->handler = $handler;
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
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @return \Closure|string
     */
    public function getHandler()
    {
        return $this->handler;
    }

//endregion Getters/Setters

}