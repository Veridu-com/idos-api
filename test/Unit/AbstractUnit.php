<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace Test\Unit;

abstract class AbstractUnit extends \PHPUnit_Framework_TestCase {
    /**
     * Sets a protected property on a given object via reflection.
     *
     * @link http://stackoverflow.com/questions/18558183/phpunit-mockbuilder-set-mock-object-internal-property
     *
     * @param mixed  $object   instance in which protected value is being modified
     * @param string $property property on instance being modified
     * @param mixed  $value    new value of the property being modified
     *
     * @return void
     */
    protected function setProtectedProperty($object, string $property, $value): void {
        $reflection         = new \ReflectionClass($object);
        $reflectionProperty = $reflection->getProperty($property);
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($object, $value);
    }

    /**
     * Manipulates a protected method's visibility on a given object via reflection.
     *
     * @param mixed  $object instance in which protected method is being manipulated
     * @param string $method instance's method name being modified
     *
     * @return \ReflectionMethod
     */
    protected function setProtectedMethod($object, string $method) : \ReflectionMethod {
        $reflection       = new \ReflectionClass($object);
        $reflectionMethod = $reflection->getMethod($method);
        $reflectionMethod->setAccessible(true);

        return $reflectionMethod;
    }

    /**
     * Invoke method $methodName from $object with $parameters.
     *
     * @param mixed  $object     The object
     * @param string $methodName The method name
     * @param array  $parameters The method parameters
     *
     * @return mixed The invoked method return
     */
    protected function invokePrivateMethod($object, string $methodName, array $parameters) {
        $reflection = new \ReflectionClass($object);

        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }
}
