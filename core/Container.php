<?php

declare(strict_types=1);

namespace DDWB;

/**
 * Simple Dependency Injection Container
 * 
 * A lightweight service container for managing dependencies
 */
final class Container
{
    /** @var array<string, \Closure> */
    private array $bindings = [];
    
    /** @var array<string, mixed> */
    private array $instances = [];

    /**
     * Bind a service to the container
     * 
     * @template T
     * @param string $abstract The service name or class
     * @param \Closure(): T $concrete The closure to create the service
     */
    public function bind(string $abstract, \Closure $concrete): void
    {
        $this->bindings[$abstract] = $concrete;
    }

    /**
     * Bind a singleton service to the container
     * 
     * @template T
     * @param string $abstract The service name or class
     * @param \Closure(): T $concrete The closure to create the service
     */
    public function singleton(string $abstract, \Closure $concrete): void
    {
        $this->bind($abstract, function () use ($abstract, $concrete) {
            if (!isset($this->instances[$abstract])) {
                $this->instances[$abstract] = $concrete();
            }
            return $this->instances[$abstract];
        });
    }

    /**
     * Resolve a service from the container
     * 
     * @template T
     * @param string $abstract The service name or class
     * @return T The resolved service
     * @throws \Exception If the service cannot be resolved
     */
    public function resolve(string $abstract): mixed
    {
        if (isset($this->bindings[$abstract])) {
            return $this->bindings[$abstract]();
        }

        if (class_exists($abstract)) {
            return $this->build($abstract);
        }

        throw new \Exception("Service [{$abstract}] not found in container.");
    }

    /**
     * Check if a service is bound
     * 
     * @param string $abstract The service name or class
     * @return bool True if the service is bound
     */
    public function has(string $abstract): bool
    {
        return isset($this->bindings[$abstract]);
    }

    /**
     * Build a class instance with dependency injection
     * 
     * @param string $class The class name
     * @return mixed The instance
     * @throws \Exception If the class cannot be instantiated
     */
    private function build(string $class): mixed
    {
        $reflector = new \ReflectionClass($class);

        if (!$reflector->isInstantiable()) {
            throw new \Exception("Class [{$class}] is not instantiable.");
        }

        $constructor = $reflector->getConstructor();

        if ($constructor === null) {
            return new $class();
        }

        $parameters = $constructor->getParameters();
        $dependencies = [];

        foreach ($parameters as $parameter) {
            $type = $parameter->getType();
            
            if ($type === null) {
                if ($parameter->isDefaultValueAvailable()) {
                    $dependencies[] = $parameter->getDefaultValue();
                } else {
                    throw new \Exception("Cannot resolve parameter [{$parameter->getName()}] in class [{$class}].");
                }
            } else {
                $typeName = $type->getName();
                
                if ($this->has($typeName)) {
                    $dependencies[] = $this->resolve($typeName);
                } elseif (class_exists($typeName)) {
                    $dependencies[] = $this->build($typeName);
                } elseif ($parameter->isDefaultValueAvailable()) {
                    $dependencies[] = $parameter->getDefaultValue();
                } else {
                    throw new \Exception("Cannot resolve type [{$typeName}] for parameter [{$parameter->getName()}] in class [{$class}].");
                }
            }
        }

        return $reflector->newInstanceArgs($dependencies);
    }
}
