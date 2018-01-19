<?php
/**
 * Created by PhpStorm.
 * User: quantick
 * Date: 19.01.18
 */

namespace Artifly\Core;

/**
 * Class DispatchedRoute
 *
 * @package Artifly\Core
 */
class DispatchedRoute
{
//region SECTION: Fields
    public const ROUTE_FOUNDED     = 'founded';
    public const ROUTE_NOT_FOUNDED = 'notfounded';

    /**
     * @var string
     */
    private $dispatchType = self::ROUTE_FOUNDED;

    /**
     * @var \Closure|string
     */
    private $handler;

    /**
     * @var Route
     */
    private $route;

    /**
     * @var array
     */
    private $args = [];
//endregion Fields

//region SECTION: Constructor
    /**
     * DispatchedRoute constructor.
     *
     * @param string          $dispatchType
     * @param \Closure|string $handler
     * @param Route           $route
     * @param array           $args
     */
    public function __construct(string $dispatchType, $handler, Route $route = null, array $args = [])
    {
        $this->dispatchType = $dispatchType;
        $this->handler      = $handler;
        $this->route        = $route;
        $this->args         = $args;
    }
//endregion Constructor

//region SECTION: Getters/Setters
    /**
     * @return string
     */
    public function getDispatchType()
    {
        return $this->dispatchType;
    }

    /**
     * @return string|\Closure
     */
    public function getHandler()
    {
        return $this->handler;
    }

    /**
     * @return Route
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * @return array
     */
    public function getArgs()
    {
        return $this->args;
    }
//endregion Getters/Setters
}