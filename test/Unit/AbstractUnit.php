<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

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
    protected function setProtectedProperty($object, string $property, $value) {
        $reflection          = new \ReflectionClass($object);
        $reflection_property = $reflection->getProperty($property);
        $reflection_property->setAccessible(true);
        $reflection_property->setValue($object, $value);
    }
}
