<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace Test\Unit\Entity;

use App\Entity\Company\Permission;
use Jenssegers\Optimus\Optimus;
use Test\Unit\AbstractUnit;

class PermissionTest extends AbstractUnit {
    /*
     * Jenssengers\Optimus\Optimus $optimus
     */
    private $optimus;

    public function setUp() {
        $this->optimus = $this->getMockBuilder(Optimus::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    private function getAttributes() {
        return [
            'id'         => 0,
            'routeName'  => 'companies:listAll',
            'public'     => 'public',
            'created_at' => time(),
            'updated_at' => time()
        ];
    }

    public function testSerialize() {
        $array = [
            'id'         => 0,
            'routeName'  => 'companies:listAll',
            'created_at' => time()
        ];

        $abstractMock = $this->getMockBuilder(Permission::class)
            ->setMethods(null)
            ->setConstructorArgs([$this->getAttributes(), $this->optimus])
            ->getMockForAbstractClass();

        $array = $abstractMock->serialize();

        $this->assertArrayHasKey('id', $array);
        $this->assertSame(0, $array['id']);

        $this->assertArrayHasKey('route_name', $array);
        $this->assertSame('companies:listAll', $array['route_name']);

        $this->assertArrayHasKey('created_at', $array);

        $this->assertTrue(is_string($array['created_at']));
        $this->assertTrue(is_int($abstractMock->createdAt));
    }

    public function testToArray() {
        $abstractMock = $this->getMockBuilder(EndpointPermission::class)
            ->setMethods(null)
            ->setConstructorArgs([$this->getAttributes(), $this->optimus])
            ->getMockForAbstractClass();

        $array = $abstractMock->toArray();

        $this->assertArrayHasKey('route_name', $array);
        $this->assertSame('companies:listAll', $array['route_name']);

        $this->assertArrayHasKey('created_at', $array);
        $this->assertTrue(is_int($array['created_at']));
    }

    public function testGetCachedKeysEmptyAttributes() {
        $array        = ['Permission.id.', 'Permission.public.'];
        $abstractMock = $this->getMockBuilder(EndpointPermission::class)
            ->setMethods(null)
            ->setConstructorArgs([[], $this->optimus])
            ->getMockForAbstractClass();
        $result = $abstractMock->getCacheKeys();
        $this->assertNotEmpty($result);
        // assertEquals: we want the array key => value combinations to be the same, but not necessarily in the same order
        $this->assertEquals($array, $result);
    }

    public function testGetCachedKeys() {
        $array        = ['Permission.id.0', 'Permission.public.public'];
        $abstractMock = $this->getMockBuilder(EndpointPermission::class)
            ->setMethods(null)
            ->setConstructorArgs([$this->getAttributes(), $this->optimus])
            ->getMockForAbstractClass();
        $result = $abstractMock->getCacheKeys();
        $this->assertNotEmpty($result);
        // assertEquals: we want the array key => value combinations to be the same, but not necessarily in the same order
        $this->assertEquals($array, $result);
    }

    public function testReferenceCacheKeysNoParentId() {
        $array        = ['Permission.by.parent_id.', 'Permission.id.0', 'Permission.public.public'];
        $abstractMock = $this->getMockBuilder(EndpointPermission::class)
            ->setMethods(null)
            ->setConstructorArgs([$this->getAttributes(), $this->optimus])
            ->getMockForAbstractClass();
        $result = $abstractMock->getReferenceCacheKeys();
        $this->assertNotEmpty($result);
        // assertEquals: we want the array key => value combinations to be the same, but not necessarily in the same order
        $this->assertEquals($array, $result);
    }

    public function testReferenceCacheKeysEmptyAttributes() {
        $array        = ['Permission.by.parent_id.', 'Permission.id.', 'Permission.public.'];
        $abstractMock = $this->getMockBuilder(EndpointPermission::class)
            ->setMethods(null)
            ->setConstructorArgs([[], $this->optimus])
            ->getMockForAbstractClass();
        $result = $abstractMock->getReferenceCacheKeys();
        $this->assertNotEmpty($result);
        // assertEquals: we want the array key => value combinations to be the same, but not necessarily in the same order
        $this->assertEquals($array, $result);
    }

    public function testReferenceCacheKeys() {
        $array        = ['Permission.by.parent_id.0', 'Permission.id.0', 'Permission.public.public'];
        $abstractMock = $this->getMockBuilder(EndpointPermission::class)
            ->setMethods(null)
            ->setConstructorArgs(
                [
                    array_merge(
                        ['parentId' => '0'],
                        $this->getAttributes()
                    ),
                    $this->optimus
                ]
            )
            ->getMockForAbstractClass();
        $result = $abstractMock->getReferenceCacheKeys();
        $this->assertNotEmpty($result);
        // assertEquals: we want the array key => value combinations to be the same, but not necessarily in the same order
        $this->assertEquals($array, $result);
    }
}
