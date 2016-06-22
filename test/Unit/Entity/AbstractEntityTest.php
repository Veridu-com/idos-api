<?php
/**
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace Test\Unit\Entity;

use App\Entity\AbstractEntity;

class AbstractEntityTest extends \PHPUnit_Framework_TestCase {

    /**
     * Sets a protected property on a given object via reflection
     *
     * @link http://stackoverflow.com/questions/18558183/phpunit-mockbuilder-set-mock-object-internal-property
     *
     * @param mixed $object instance in which protected value is being modified
     * @param string $property property on instance being modified
     * @param mixed $value new value of the property being modified
     *
     * @return void
     */
    private function setProtectedProperty($object, $property, $value) {
        $reflection = new \ReflectionClass($object);
        $reflection_property = $reflection->getProperty($property);
        $reflection_property->setAccessible(true);
        $reflection_property->setValue($object, $value);
    }

    public function testToArray() {
        $array = [
            'id' => 0,
            'name' => 'abc'
        ];
        $abstractMock = $this->getMockBuilder(AbstractEntity::class)
            ->setMethods(null)
            ->setConstructorArgs(['attributes' => $array])
            ->getMockForAbstractClass();
        $this->assertSame($array, $abstractMock->toArray());
    }

    public function testToPublicArray() {
        $abstractMock = $this->getMockBuilder(AbstractEntity::class)
            ->setMethods(null)
            ->setConstructorArgs(
                [
                    'attributes' => [
                        'id' => 0,
                        'name' => 'abc'
                    ]
                ]
            )
            ->getMockForAbstractClass();
        $this->setProtectedProperty($abstractMock, 'visible', ['name']);

        $this->assertSame(['name' => 'abc'], $abstractMock->toPublicArray());
    }

    public function testExists() {
        $abstractMock = $this->getMockBuilder(AbstractEntity::class)
            ->setMethods(null)
            ->setConstructorArgs(
                [
                    'attributes' => [
                        'id' => 0,
                        'name' => 'abc'
                    ]
                ]
            )
            ->getMockForAbstractClass();
        $this->assertTrue($abstractMock->exists());
        $this->assertSame(0, $abstractMock->id);
        $this->assertSame('abc', $abstractMock->name);
    }

    public function testNotExists() {
        $abstractMock = $this->getMockBuilder(AbstractEntity::class)
            ->setMethods(null)
            ->getMockForAbstractClass();
        $abstractMock->hydrate(
            [
                'id' => 0,
                'name' => 'abc'
            ]
        );
        $this->assertFalse($abstractMock->exists());
        $this->assertSame(0, $abstractMock->id);
        $this->assertSame('abc', $abstractMock->name);
    }

    public function testIsNotDirty() {
        $abstractMock = $this->getMockBuilder(AbstractEntity::class)
            ->setMethods(null)
            ->setConstructorArgs(
                [
                    'attributes' => [
                        'id' => 0,
                        'name' => 'abc'
                    ]
                ]
            )
            ->getMockForAbstractClass();
        $this->assertFalse($abstractMock->isDirty());
    }
    public function testIsDirty() {
        $abstractMock = $this->getMockBuilder(AbstractEntity::class)
            ->setMethods(null)
            ->setConstructorArgs(
                [
                    'attributes' => [
                        'id' => 0,
                        'name' => 'abc'
                    ]
                ]
            )
            ->getMockForAbstractClass();
        $this->assertFalse($abstractMock->isDirty());
        $abstractMock->name = 'cba';
        $this->assertTrue($abstractMock->isDirty());
    }
    public function testMagicMethods() {
        $abstractMock = $this->getMockBuilder(AbstractEntity::class)
            ->setMethods(null)
            ->setConstructorArgs(
                [
                    'attributes' => [
                        'id' => 0,
                        'name' => 'abc'
                    ]
                ]
            )
            ->getMockForAbstractClass();

        $this->assertTrue(isset($abstractMock->name));
        $this->assertSame('abc', $abstractMock->name);
        $abstractMock->name = 'cba';
        $this->assertSame('cba', $abstractMock->name);
        unset($abstractMock->name);
        $this->assertFalse(isset($abstractMock->name));
    }

    public function testSetMutator() {
        $abstractMock = $this->getMockBuilder(AbstractEntity::class)
            ->setMethods(['setNameAttribute'])
            ->setConstructorArgs(
                [
                    'attributes' => [
                        'id' => 0,
                        'name' => 'abc'
                    ]
                ]
            )
            ->getMockForAbstractClass();

        $abstractMock
            ->expects($this->once())
            ->method('setNameAttribute')
            ->with($this->equalTo('cba'));

        $abstractMock->name = 'cba';
    }

    public function testGetMutator() {
        $abstractMock = $this->getMockBuilder(AbstractEntity::class)
            ->setMethods(['getNameAttribute'])
            ->setConstructorArgs(
                [
                    'attributes' => [
                        'id' => 0,
                        'name' => 'abc'
                    ]
                ]
            )
            ->getMockForAbstractClass();

        $abstractMock
            ->expects($this->once())
            ->method('getNameAttribute')
            ->with($this->equalTo('abc'))
            ->will($this->returnValue('cba'));

        $this->assertSame('cba', $abstractMock->name);
    }
}
