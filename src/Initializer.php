<?php

namespace Faulancer;

use Apix\Log\Logger;
use Assert\Assert;
use ReflectionClass;
use ReflectionParameter;
use ReflectionException;
use JetBrains\PhpStorm\Pure;
use Assert\InvalidArgumentException;
use Psr\Container\ContainerInterface;
use Faulancer\Exception\NotFoundException;

/**
 * Class Initializer
 */
class Initializer
{

    private static array $cache = [];

    private static ContainerInterface $container;

    /**
     * @param Container $container
     */
    public static function setContainer(Container $container): void
    {
        self::$container = $container;
    }

    /**
     * @return ContainerInterface
     */
    public static function getContainer(): ContainerInterface
    {
        return self::$container;
    }

    /**
     * @param string $className
     * @param array  $constructorArgs
     * @param bool   $newInstance
     *
     * @return object|null
     * @throws Exception\ContainerException
     * @throws NotFoundException
     */
    public static function load(string $className, array $constructorArgs = [], bool $newInstance = false): ?object
    {
        try {
            Assert::that($className)->classExists();
            Assert::that($className)->objectOrClass('You probably tried to initialize an Interface: %s.');

            if (self::$container->has($className) && false === $newInstance) {
                return self::$container->get($className);
            }

            $reflection = new ReflectionClass($className);
            $awareInterfaceDependencies = static::extractAwareInterfaceDependencies($reflection->getInterfaces());

            $obj = $reflection->hasMethod('__construct')
                ? $reflection->newInstanceArgs($constructorArgs)
                : $reflection->newInstanceWithoutConstructor();

            foreach ($awareInterfaceDependencies as $setter => $awareInterfaceDependency) {
                try {
                    $awareInterfaceDependencyObject = static::getContainer()->get($awareInterfaceDependency);
                } catch (NotFoundException $e) {

                    $awareInterfaceDependencyObject = static::load($awareInterfaceDependency);
                    static::getContainer()->set($awareInterfaceDependency, $awareInterfaceDependencyObject);
                    return static::load($className, $constructorArgs);
                }

                $obj->$setter($awareInterfaceDependencyObject);
            }

            if (false === $newInstance) {
                self::$container->set($className, $obj);
            }

            return $obj;

        } catch (ReflectionException | InvalidArgumentException $e) {
            throw new NotFoundException($e->getMessage(), [], $e->getCode(), $e->getPrevious());
        }
    }

    /**
     * @return Logger
     * @throws Exception\ContainerException
     * @throws NotFoundException
     */
    private static function getLogger(): Logger
    {
        return self::load(Logger::class);
    }

    /**
     * @param ReflectionClass $reflection
     *
     * @return array
     * @throws Exception\ContainerException
     * @throws NotFoundException
     */
//    private static function getConstructorDependencies(ReflectionClass $reflection): array
//    {
//        $constructorDependencyObjects = [];
//        $constructorDependencies = self::extractParameterDependencies($reflection->getConstructor()->getParameters());
//
//        foreach ($constructorDependencies as $parameter => $constructorDependency) {
//            try {
//                $constructorDependencyObjects[$parameter] = self::getContainer()->get($constructorDependency);
//            } catch (NotFoundException $e) {
//                self::$cache[$constructorDependency] = self::load($constructorDependency);
//                $constructorDependencyObjects[$parameter] = self::$cache[$constructorDependency];
//            }
//        }
//
//        return $constructorDependencyObjects;
//    }

    /**
     * @param ReflectionParameter[] $parameters
     *
     * @return array
     */
    #[Pure] private static function extractParameterDependencies(array $parameters): array
    {
        $result = [];

        foreach ($parameters as $parameter) {
            if (null === $parameter->getType()) {
                continue;
            }

            $result[$parameter->getName()] = $parameter->getType()->getName();
        }

        return $result;
    }

    /**
     * @param ReflectionClass[] $interfaces
     *
     * @return array
     */
    #[Pure] private static function extractAwareInterfaceDependencies(array $interfaces): array
    {
        $result = [];

        foreach ($interfaces as $interface) {
            if (false === str_contains($interface->getName(), 'AwareInterface')) {
                continue;
            }

            foreach ($interface->getMethods() as $method) {
                if (false === str_starts_with($method->getName(), 'set')) {
                    continue;
                }

                $result[$method->getName()] = $method->getParameters()[0]->getType()->getName();
            }
        }

        return $result;
    }
}
