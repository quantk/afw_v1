<?php
/**
 * Created by PhpStorm.
 * User: quantick
 * Date: 19.01.18
 */

namespace Artifly\Core;


use Artifly\Core\Exception\ConflictServiceError;
use Artifly\Core\Exception\ServiceNotFoundError;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Class Container
 *
 * @package Artifly\Core
 */
class Container implements ContainerInterface
{
//region SECTION: Fields
    /**
     * @var array
     */
    private $services = [];
//endregion Fields

//region SECTION: Constructor
    /**
     * Container constructor.
     */
    public function __construct()
    {
        $this->addInstance($this);
    }
//endregion Constructor

//region SECTION: Public

    /**
     * @param $id
     *
     * @return $this
     * @throws ConflictServiceError
     */
    public function add($id): Container
    {
        if (isset($this->services[$id])) {
            throw new ConflictServiceError();
        }

        $this->services[$id] = [
            'instance' => null,
            'public' => true
        ];

        return $this;
    }

    /**
     * @param mixed $instance
     *
     * @return Container
     */
    public function addInstance($instance): Container
    {
        $id = $this->getServiceId($instance);
        $this->add($id);
        $this->setInstanceById($id, $instance);

        return $this;
    }

    /**
     * Returns true if the container can return an entry for the given identifier.
     * Returns false otherwise.
     *
     * `has($id)` returning true does not mean that `get($id)` will not throw an exception.
     * It does however mean that `get($id)` will not throw a `NotFoundExceptionInterface`.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @return bool
     */
    public function has($id): bool
    {
        return $this->isServiceExist($id) && $this->isInitialized($id);
    }
//endregion Public

//region SECTION: Private
    /**
     * @param $id
     *
     * @return bool
     */
    private function isServiceExist($id): bool
    {
        return array_key_exists($id, $this->services);
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    private function initialize($id)
    {
        // If not initialized
        if ($this->getInstanceById($id) === null) {
            $this->setInstanceById($id, $this->resolveDependency($id));
        }

        return $this->getInstanceById($id);
    }

    /**
     * @param $service
     *
     * @return string
     */
    private function getServiceId($service): string
    {
        return get_class($service);
    }

    /**
     * @param $id
     *
     * @return bool
     */
    private function isInitialized($id): bool
    {
        $instance = $this->getInstanceById($id);
        return isset($instance);
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    private function getInstanceById($id)
    {
        return $this->services[$id]['instance'];
    }

    /**
     * @param $id
     * @param $instance
     */
    private function setInstanceById($id, $instance)
    {
        $this->services[$id]['instance'] = $instance;
    }

    /**
     * @param \ReflectionClass $class
     * @param array            $args
     *
     * @return mixed
     */
    private function createFromReflection(\ReflectionClass $class, $args = [])
    {
        $instance            = $class->newInstanceArgs($args);
        $id                  = $this->getServiceId($instance);
        $this->setInstanceById($id, $instance);

        return $this->getInstanceById($id);
    }

    /**
     * @param $service
     *
     * @return mixed|object
     */
    private function resolveDependency($service)
    {
        if ($this->isInitialized($service)) {
            return $this->getInstanceById($service);
        } else {
            $rService = new \ReflectionClass($service);

            $constructor = $rService->getConstructor();

            if ($constructor === null) {
                return $this->createFromReflection($rService);
            }

            $parameters = $constructor->getParameters();
            $args       = [];
            foreach ($parameters as $parameter) {
                $dependencyClass = $parameter->getClass()->getName();

                if ($this->isServiceExist($dependencyClass) && $this->isInitialized($dependencyClass)) {
                    $args[] = $this->getInstanceById($dependencyClass);
                } else {
                    $args[] = $this->resolveDependency($dependencyClass);
                }
            }

            return $this->createFromReflection($rService, $args);
        }
    }
//endregion Private

//region SECTION: Getters/Setters
    /**
     * Finds an entry of the container by its identifier and returns it.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @throws NotFoundExceptionInterface  No entry was found for **this** identifier.
     * @throws ContainerExceptionInterface Error while retrieving the entry.
     *
     * @return mixed Entry.
     */
    public function get($id)
    {
        if (!$this->isServiceExist($id)) {
            throw new ServiceNotFoundError();
        }

        $this->setInstanceById($id, $this->initialize($id));

        return $this->getInstanceById($id);
    }
//endregion Getters/Setters
}