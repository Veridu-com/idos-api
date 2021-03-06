<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace Test\Unit\Entity;

use App\Entity\Company;
use Jenssegers\Optimus\Optimus;
use Test\Unit\AbstractUnit;

class CompanyTest extends AbstractUnit {
    /*
     * Jenssengers\Optimus\Optimus $optimus
     */
    private $optimus;

    public function setUp() {
        $this->optimus = $this->getMockBuilder(Optimus::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testSerialize() {
        $updated = time();
        $array   = [
            'id'          => 0,
            'name'        => 'My Co',
            'public_key'  => 'pkey',
            'private_key' => 'privkey',
            'created_at'  => time(),
            'updated_at'  => time()
        ];

        $abstractMock = $this->getMockBuilder(Company::class)
            ->setMethods(null)
            ->setConstructorArgs([$array, $this->optimus])
            ->getMockForAbstractClass();
        $array = $abstractMock->serialize();
        $this->assertArrayHasKey('id', $array);
        $this->assertSame(0, $array['id']);
        $this->assertArrayHasKey('name', $array);
        $this->assertSame('My Co', $array['name']);
        $this->assertArrayHasKey('slug', $array);
        $this->assertSame('my-co', $array['slug']);
        $this->assertArrayHasKey('public_key', $array);
        $this->assertSame('pkey', $array['public_key']);
        $this->assertArrayHasKey('private_key', $array);
        $this->assertNotSame('privkey', $array['private_key']);
        $this->assertStringStartsWith('secure:', $array['private_key']);
        $this->assertArrayHasKey('created_at', $array);
        $this->assertTrue(is_string($array['created_at']));
        $this->assertTrue(is_int($abstractMock->createdAt));
        $this->assertArrayHasKey('updated_at', $array);
        $this->assertTrue(is_string($array['updated_at']));
        $this->assertTrue(is_int($abstractMock->updatedAt));
    }

    public function testToArray() {
        $abstractMock = $this->getMockBuilder(Company::class)
            ->setMethods(null)
            ->setConstructorArgs(
                [
                    [
                        'id'          => 0,
                        'name'        => 'My Co',
                        'public_key'  => 'pkey',
                        'private_key' => 'privkey',
                        'created_at'  => time(),
                        'updated_at'  => time()
                    ],
                    $this->optimus
                ]
            )
            ->getMockForAbstractClass();

        $array = $abstractMock->toArray();
        $this->assertArrayHasKey('name', $array);
        $this->assertSame('My Co', $array['name']);
        $this->assertArrayHasKey('slug', $array);
        $this->assertSame('my-co', $array['slug']);
        $this->assertArrayHasKey('public_key', $array);
        $this->assertSame('pkey', $array['public_key']);
        $this->assertArrayHasKey('created_at', $array);
        $this->assertTrue(is_int($array['created_at']));
    }

    public function testGetCachedKeysEmptyAttributes() {
        $array        = ['Company.id.', 'Company.slug.', 'Company.private_key.'];
        $abstractMock = $this->getMockBuilder(Company::class)
            ->setMethods(null)
            ->setConstructorArgs([[], $this->optimus])
            ->getMockForAbstractClass();
        $result = $abstractMock->getCacheKeys();
        $this->assertNotEmpty($result);
        // assertEquals: we want the array key => value combinations to be the same, but not necessarily in the same order
        $this->assertEquals($array, $result);
    }

    public function testGetCachedKeys() {
        $array        = ['Company.id.0', 'Company.slug.my-co', 'Company.private_key.privkey'];
        $abstractMock = $this->getMockBuilder(Company::class)
            ->setMethods(null)
            ->setConstructorArgs(
                [
                    [
                        'id'          => 0,
                        'name'        => 'My Co',
                        'public_key'  => 'pkey',
                        'private_key' => 'privkey',
                        'created_at'  => time(),
                        'updated_at'  => time()
                    ],
                    $this->optimus
                ]
            )
            ->getMockForAbstractClass();
        $result = $abstractMock->getCacheKeys();
        $this->assertNotEmpty($result);
        // assertEquals: we want the array key => value combinations to be the same, but not necessarily in the same order
        $this->assertEquals($array, $result);
    }

    public function testReferenceCacheKeysNoParentId() {
        $array        = ['Company.by.parent_id.', 'Company.id.0', 'Company.slug.my-co', 'Company.private_key.privkey'];
        $abstractMock = $this->getMockBuilder(Company::class)
            ->setMethods(null)
            ->setConstructorArgs(
                [
                    [
                        'id'          => 0,
                        'name'        => 'My Co',
                        'public_key'  => 'pkey',
                        'private_key' => 'privkey',
                        'created_at'  => time(),
                        'updated_at'  => time()
                    ],
                    $this->optimus
                ]
            )
            ->getMockForAbstractClass();
        $result = $abstractMock->getReferenceCacheKeys();
        $this->assertNotEmpty($result);
        // assertEquals: we want the array key => value combinations to be the same, but not necessarily in the same order
        $this->assertEquals($array, $result);
    }

    public function testReferenceCacheKeysEmptyAttributes() {
        $array        = ['Company.by.parent_id.', 'Company.id.', 'Company.slug.', 'Company.private_key.'];
        $abstractMock = $this->getMockBuilder(Company::class)
            ->setMethods(null)
            ->setConstructorArgs([[], $this->optimus])
            ->getMockForAbstractClass();
        $result = $abstractMock->getReferenceCacheKeys();
        $this->assertNotEmpty($result);
        // assertEquals: we want the array key => value combinations to be the same, but not necessarily in the same order
        $this->assertEquals($array, $result);
    }

    public function testReferenceCacheKeys() {
        $array        = ['Company.by.parent_id.parentId', 'Company.id.0', 'Company.slug.my-co', 'Company.private_key.privkey'];
        $abstractMock = $this->getMockBuilder(Company::class)
            ->setMethods(null)
            ->setConstructorArgs(
                [
                    [
                        'parent_id'   => 'parentId',
                        'id'          => 0,
                        'name'        => 'My Co',
                        'public_key'  => 'pkey',
                        'private_key' => 'privkey',
                        'created_at'  => time(),
                        'updated_at'  => time()
                    ],
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
