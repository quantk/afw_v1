<?php
/**
 * Created by PhpStorm.
 * User: quantick
 * Date: 19.01.18
 */

namespace Artifly\Core;


use Artifly\Core\Exception\ContainerNotInjectedError;
use Psr\Container\ContainerInterface;

/**
 * Class ActionHandler
 *
 * @package Artifly\Core
 */
class ActionHandler
{
//region SECTION: Fields
    public const CONTROLLER_TYPE = 'controller';
    public const CLOSURE_TYPE    = 'closure';
    public const CONTROLLER_PATH = 'App\\Controller\\';

    private $handlerType = self::CONTROLLER_TYPE;

    /**
     * @var \Closure
     */
    private $handler;

    /**
     * @var string
     */
    private $controllerClass;

    /**
     * @var Container|null
     */
    private $container;

    /**
     * @var string
     */
    private $action;
//endregion Fields

//region SECTION: Constructor
    /**
     * ActionHandler constructor.
     *
     * @param string $handlerType
     * @param        $handler
     */
    public function __construct($handler, string $handlerType = self::CONTROLLER_TYPE)
    {
        $this->handlerType = $handlerType;
        if ($handler instanceof \Closure) {
            $this->handler = $handler;
        } else {
            list($controllerClassName, $action) = $this->parseHandlerString($handler);
            $this->controllerClass = sprintf("%s%s", self::CONTROLLER_PATH, $controllerClassName);
            $this->action          = $action;
        }
    }
//endregion Constructor

//region SECTION: Public
    /**
     * @param $args
     *
     * @return mixed|string
     */
    public function execute($args)
    {
        $content = '';
        if (!$this->container instanceof ContainerInterface) {
            throw new ContainerNotInjectedError();
        }
        switch ($this->handlerType) {
            case self::CONTROLLER_TYPE:
                if (!$this->container->has($this->controllerClass)) {
                    $this->container->add($this->controllerClass);
                }
                $controller   = $this->container->get($this->controllerClass);
                $rAction      = new \ReflectionMethod($controller, $this->action);
                $dependencies = array_merge($this->resolveMethodParams($rAction), $args);

                $content = call_user_func_array([$controller, $this->action], $dependencies);
                break;
            case self::CLOSURE_TYPE:
                $rHandler     = new \ReflectionFunction($this->handler);
                $dependencies = array_merge($this->resolveMethodParams($rHandler), $args);
                $content      = call_user_func_array($this->handler, $dependencies);
                break;
        }

        return $content;
    }
//endregion Public

//region SECTION: Private
    /**
     * @param \ReflectionMethod|\ReflectionFunction $rHandler
     *
     * @return array
     * @throws Exception\ConflictServiceError
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    private function resolveMethodParams($rHandler): array
    {
        $dependencies = [];
        foreach ($rHandler->getParameters() as $parameter) {
            if ($parameter->getClass()) {
                $className = $parameter->getClass()->getName();
                if (!$this->container->has($className)) {
                    $this->container->add($className);
                }
                $dependencies[] = $this->container->get($className);
            }
        }

        return $dependencies;
    }

    /**
     * @param string $handler
     *
     * @return array
     */
    private function parseHandlerString(string $handler): array
    {
        $controllerHandlerParts = explode('@', $handler);
        $controllerClassName    = $controllerHandlerParts[0];
        $action                 = $controllerHandlerParts[1];

        return array($controllerClassName, $action);
    }
//endregion Private

//region SECTION: Getters/Setters
    /**
     * @param Container|null $container
     *
     * @return ActionHandler
     */
    public function setContainer($container)
    {
        $this->container = $container;

        return $this;
    }
//endregion Getters/Setters
}