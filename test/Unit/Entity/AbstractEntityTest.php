<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace Test\Unit\Entity;

use App\Entity\AbstractEntity;
use Test\Unit\AbstractUnit;

class AbstractEntityTest extends AbstractUnit {
    public function testSerialize() {
        $array = [
            'id'   => 0,
            'name' => 'abc'
        ];
        $abstractMock = $this->getMockBuilder(AbstractEntity::class)
            ->setMethods(['getReferenceCacheKeys'])
            ->setConstructorArgs(['attributes' => $array])
            ->getMockForAbstractClass();

        $this->assertSame($array, $abstractMock->serialize());
    }

    public function testToArray() {
        $abstractMock = $this->getMockBuilder(AbstractEntity::class)
            ->setMethods(['getReferenceCacheKeys'])
            ->setConstructorArgs(
                [
                    'attributes' => [
                        'id'   => 0,
                        'name' => 'abc'
                    ]
                ]
            )
            ->getMockForAbstractClass();
        $this->setProtectedProperty($abstractMock, 'visible', ['name']);

        $this->assertSame(['name' => 'abc'], $abstractMock->toArray());
    }

    public function testExists() {
        $abstractMock = $this->getMockBuilder(AbstractEntity::class)
            ->setMethods(['getReferenceCacheKeys'])
            ->setConstructorArgs(
                [
                    'attributes' => [
                        'id'   => 0,
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
            ->setMethods(['getReferenceCacheKeys'])
            ->getMockForAbstractClass();
        $abstractMock->hydrate(
            [
                'id'   => 0,
                'name' => 'abc'
            ]
        );
        $this->assertFalse($abstractMock->exists());
        $this->assertSame(0, $abstractMock->id);
        $this->assertSame('abc', $abstractMock->name);
    }

    public function testIsNotDirty() {
        $abstractMock = $this->getMockBuilder(AbstractEntity::class)
            ->setMethods(['getReferenceCacheKeys'])
            ->setConstructorArgs(
                [
                    'attributes' => [
                        'id'   => 0,
                        'name' => 'abc'
                    ]
                ]
            )
            ->getMockForAbstractClass();
        $this->assertFalse($abstractMock->isDirty());
    }
    public function testIsDirty() {
        $abstractMock = $this->getMockBuilder(AbstractEntity::class)
            ->setMethods(['getReferenceCacheKeys'])
            ->setConstructorArgs(
                [
                    'attributes' => [
                        'id'   => 0,
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
            ->setMethods(['getReferenceCacheKeys'])
            ->setConstructorArgs(
                [
                    'attributes' => [
                        'id'   => 0,
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
            ->setMethods(['getReferenceCacheKeys'])
            ->setConstructorArgs(
                [
                    'attributes' => [
                        'id'   => 0,
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
                        'id'   => 0,
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
