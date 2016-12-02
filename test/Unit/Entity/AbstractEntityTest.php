<?php
/*
 * Copyright (c) ,12-,16 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace Test\Unit\Entity;

use App\Entity\AbstractEntity;
use App\Entity\Company;
use Jenssegers\Optimus\Optimus;
use Test\Unit\AbstractUnit;

class AbstractEntityTest extends AbstractUnit {
    /*
     * Jenssengers\Optimus\Optimus $optimus
     */
    private $optimus;

    public function setUp() {
        $this->optimus = $this->getMockBuilder(Optimus::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testToCamelCase() {
        $array = [
            'id'   => 1,
            'name' => 'New Abstract Entity'
        ];
        $abstractMock = $this->getMockBuilder(AbstractEntity::class)
            ->setMethods(['getReferenceCacheKeys'])
            ->setConstructorArgs([$array, $this->optimus])
            ->getMockForAbstractClass();

        $method = $this->setProtectedMethod($abstractMock, 'toCamelCase');

        $this->assertSame('Abc', $method->invoke($abstractMock, 'abc'));
    }

    public function testToSnakeCase() {
        $array = [
            'id'   => 1,
            'name' => 'New Abstract Entity'
        ];
        $abstractMock = $this->getMockBuilder(AbstractEntity::class)
            ->setMethods(['getReferenceCacheKeys'])
            ->setConstructorArgs([$array, $this->optimus])
            ->getMockForAbstractClass();

        $method = $this->setProtectedMethod($abstractMock, 'toSnakeCase');

        $this->assertSame('_abc_de', $method->invoke($abstractMock, 'AbcDe'));
    }

    public function testHasSetMutator() {
        $abstractMock = $this->getMockBuilder(Company::class)
            ->setMethods(['getReferenceCacheKeys', 'setNameAttribute'])
            ->setConstructorArgs(
                [
                [
                    'id'   => 1,
                    'name' => 'abc'
                ],
                $this->optimus
                ]
            )
            ->getMockForAbstractClass();

        $abstractMock
            ->expects($this->once())
            ->method('setNameAttribute')
            ->with($this->equalTo('cba'))
            ->will($this->returnValue($abstractMock));

        $abstractMock->name = 'cba';

        $method = $this->setProtectedMethod($abstractMock, 'hasSetMutator');

        $this->assertTrue($method->invoke($abstractMock, 'name'));
    }

    public function testHasGetMutator() {
        $abstractMock = $this->getMockBuilder(AbstractEntity::class)
            ->setMethods(['getNameAttribute'])
            ->setConstructorArgs(
                [
                    [
                        'id'   => 1,
                        'name' => 'abc'
                    ],
                    $this->optimus
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

    public function testSetAttributeHasSetMutator() {
        $array = [
            'id'   => 1,
            'name' => 'abc'
        ];
        $abstractMock = $this->getMockBuilder(AbstractEntity::class)
            ->setMethods(['getReferenceCacheKeys', 'hasSetMutator'])
            ->setConstructorArgs([$array, $this->optimus])
            ->getMockForAbstractClass();
        $abstractMock
            ->method('hasSetMutator')
            ->will($this->returnValue(true));
        $method = $this->setProtectedMethod($abstractMock, 'setAttribute');

        $this->assertInstanceOf(AbstractEntity::class, $method->invoke($abstractMock, 'name', 'value'));
    }

    public function testSetAttributeHasRelation() {
        $array = [
            'id'   => 1,
            'name' => 'abc'
        ];
        $abstractMock = $this->getMockBuilder(AbstractEntity::class)
            ->setMethods(['getReferenceCacheKeys'])
            ->setConstructorArgs([$array, $this->optimus])
            ->getMockForAbstractClass();

        $method = $this->setProtectedMethod($abstractMock, 'setAttribute');

        $this->assertEmpty($abstractMock->relations);

        $method->invoke($abstractMock, 'endpoint.name', 'Endpoint Name');
        $method->invoke($abstractMock, 'endpoint.created_at', time());
        $method->invoke($abstractMock, 'endpoint.updated_at', time());
        $this->assertArrayHasKey('endpoint', $abstractMock->relations);
        $this->assertSame('Endpoint Name', $abstractMock->relations['endpoint']['name']);
        $this->assertTrue(is_int($abstractMock->relations['endpoint']['created_at']));
        $this->assertTrue(is_int($abstractMock->relations['endpoint']['updated_at']));
    }

    public function testGetAttribute() {
        $array = [
            'id'   => 1,
            'name' => 'abc'
        ];
        $abstractMock = $this->getMockBuilder(AbstractEntity::class)
            ->setMethods(['getReferenceCacheKeys', 'hasGetMutator'])
            ->setConstructorArgs([$array, $this->optimus])
            ->getMockForAbstractClass();
        $abstractMock
            ->method('hasGetMutator')
            ->will($this->returnValue(true));
        $method = $this->setProtectedMethod($abstractMock, 'getAttribute');

        $this->assertSame('abc', $method->invoke($abstractMock, 'name'));
    }

    public function testHydrate() {
        $array = [
            'id'         => 1,
            'name'       => 'Abstract Entity',
            'created_at' => time(),
            'updated_at' => time()
         ];
        $abstractMock = $this->getMockBuilder(AbstractEntity::class)
            ->setMethods(['getReferenceCacheKeys'])
            ->setConstructorArgs([$array, $this->optimus])
            ->getMockForAbstractClass();

        $abstractMock->hydrate($array);
        $this->assertSame(1, $abstractMock->id);
        $this->assertSame('Abstract Entity', $abstractMock->name);
        $this->assertTrue(is_int($abstractMock->createdAt));
        $this->assertTrue(is_int($abstractMock->updatedAt));
    }

    public function testToArray() {
        $abstractMock = $this->getMockBuilder(AbstractEntity::class)
            ->setMethods(['getReferenceCacheKeys'])
            ->setConstructorArgs(
                [
                    [
                        'id'   => 1,
                        'name' => 'abc'
                    ],
                    $this->optimus
                ]
            )
            ->getMockForAbstractClass();
        $this->setProtectedProperty($abstractMock, 'visible', ['name']);

        $this->assertSame(['name' => 'abc'], $abstractMock->toArray());
    }

    public function testSerialize() {
        $array = [
            'id'   => 1,
            'name' => 'abc'
        ];
        $abstractMock = $this->getMockBuilder(AbstractEntity::class)
            ->setMethods(['getReferenceCacheKeys'])
            ->setConstructorArgs([$array, $this->optimus])
            ->getMockForAbstractClass();

        $this->assertSame($array, $abstractMock->serialize());
    }

    public function testExists() {
        $abstractMock = $this->getMockBuilder(AbstractEntity::class)
            ->setMethods(['getReferenceCacheKeys'])
            ->setConstructorArgs(
                [
                    [
                        'id'   => 1,
                        'name' => 'abc'
                    ],
                    $this->optimus
                ]
            )
            ->getMockForAbstractClass();
        $this->assertTrue($abstractMock->exists());
        $this->assertSame(1, $abstractMock->id);
        $this->assertSame('abc', $abstractMock->name);
    }

    public function testNotExists() {
        $abstractMock = $this->getMockBuilder(AbstractEntity::class)
            ->setMethods(['getReferenceCacheKeys'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $abstractMock->hydrate(
            [
                'id'   => 1,
                'name' => 'abc'
            ]
        );
        $this->assertFalse($abstractMock->exists());
        $this->assertSame(1, $abstractMock->id);
        $this->assertSame('abc', $abstractMock->name);
    }

    public function testIsNotDirty() {
        $abstractMock = $this->getMockBuilder(AbstractEntity::class)
            ->setMethods(['getReferenceCacheKeys'])
            ->setConstructorArgs(
                [
                    [
                        'id'   => 1,
                        'name' => 'abc'
                    ],
                    $this->optimus
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
                    [
                        'id'   => 1,
                        'name' => 'abc'
                    ],
                    $this->optimus
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
                    [
                        'id'   => 1,
                        'name' => 'abc'
                    ],
                    $this->optimus
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
            ->setMethods(['getReferenceCacheKeys', 'setNameAttribute'])
            ->setConstructorArgs([[], $this->optimus])
            ->getMockForAbstractClass();

        $abstractMock
            ->expects($this->once())
            ->method('setNameAttribute')
            ->with($this->equalTo('cba'))
            ->will($this->returnValue($abstractMock));

        $abstractMock->name = 'cba';
    }

    public function testGetMutator() {
        $abstractMock = $this->getMockBuilder(AbstractEntity::class)
            ->setMethods(['getNameAttribute'])
            ->setConstructorArgs(
                [
                    [
                        'id'   => 0,
                        'name' => 'abc'
                    ],
                    $this->optimus
                ]
            )
            ->getMockForAbstractClass();
    }
}
