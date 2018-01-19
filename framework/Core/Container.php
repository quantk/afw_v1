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

        $this->services[$id] = null;

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
        $this->services[$id] = $instance;

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
        if ($this->services[$id] === null) {
            $this->services[$id] = $this->resolveDependency($id);
        }

        return $this->services[$id];
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
        return isset($this->services[$id]);
    }

    /**
     * @param $service
     *
     * @return mixed|object
     */
    private function resolveDependency($service)
    {
        if ($this->isInitialized($service)) {
            return $this->services[$service];
        } else {
            $rService = new \ReflectionClass($service);

            $constructor = $rService->getConstructor();

            if ($constructor === null) {
                return $rService->newInstance();
            }

            $parameters = $constructor->getParameters();
            $args       = [];
            foreach ($parameters as $parameter) {
                $dependencyClass = $parameter->getClass()->getName();

                $args = [];
                if ($this->isServiceExist($dependencyClass)) {
                    $args[] = $this->services[$dependencyClass];
                } else {
                    $args[] = $this->resolveDependency($dependencyClass);
                }
            }

            return $rService->newInstanceArgs($args);
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

        $this->services[$id] = $this->initialize($id);

        return $this->services[$id];
    }
//endregion Getters/Setters
}