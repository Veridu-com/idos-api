<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace Test\Unit\Entity;

use App\Entity\Member;
use Test\Unit\AbstractUnit;

class MemberTest extends AbstractUnit {

    public function testSetAttributeWithRelation() {
        $member = new Member([
            'id' => 1,
            'role' => 'admin',
            'created_at' => time(),
            'updated_at' => time(),
            'user.username' => 'User Name',
            'user.created_at' => time(),
            'user.updated_at' => time()
        ]);

        $array = [
            'username' => 'User Name',
            'created_at' => time(),
            'updated_at' => time()
        ];

        $this->assertArrayHasKey('user', $member->relations);
        $this->assertSame($array, $member->relations['user']);
    }

    public function testSerialize() {
        $array   = [
            'id'               => 1,
            'user_id'          => 1,
            'role'             => 'admin',
            'created_at'       => time(),
            'updated_at'       => time()
        ];

        $abstractMock = $this->getMockBuilder(Member::class)
            ->setMethods(null)
            ->setConstructorArgs([$array])
            ->getMockForAbstractClass();
        $array = $abstractMock->serialize();
        $this->assertArrayHasKey('id', $array);
        $this->assertSame(1, $array['id']);
        $this->assertArrayHasKey('user_id', $array);
        $this->assertSame(1, $array['user_id']);
        $this->assertArrayHasKey('role', $array);
        $this->assertSame('admin', $array['role']);
        $this->assertArrayHasKey('created_at', $array);
        $this->assertTrue(is_string($array['created_at']));
        $this->assertTrue(is_int($abstractMock->createdAt));
        $this->assertArrayHasKey('updated_at', $array);
        $this->assertTrue(is_string($array['updated_at']));
        $this->assertTrue(is_int($abstractMock->updatedAt));
    }

    public function testToArray() {
         $array   = [
            'id'               => 1,
            'user_id'          => 1,
            'role'             => 'admin',
            'created_at'       => time(),
            'updated_at'       => time()
        ];
        $abstractMock = $this->getMockBuilder(Member::class)
            ->setMethods(null)
            ->setConstructorArgs([$array])
            ->getMockForAbstractClass();
        $array = $abstractMock->toArray();
        $this->assertArrayHasKey('user', $array);
        $this->assertArrayHasKey('role', $array);
        $this->assertSame('admin', $array['role']);
        $this->assertArrayHasKey('created_at', $array);
        $this->assertTrue(is_int($abstractMock->createdAt));
    }

    public function testGetCachedKeysEmptyAttributes() {
        $array        = ['Member.id.', 'Member.user_id.'];
        $abstractMock = $this->getMockBuilder(Member::class)
            ->setMethods(null)
            ->setConstructorArgs([])
            ->getMockForAbstractClass();
        $result = $abstractMock->getCacheKeys();
        $this->assertNotEmpty($result);
        $this->assertSame($array, $result);
    }

    public function testGetCachedKeys() {
        $array        = ['Member.id.1', 'Member.user_id.1'];
        $abstractMock = $this->getMockBuilder(Member::class)
            ->setMethods(null)
            ->setConstructorArgs([
                [
                    'id'         => 1,
                    'user_id'    => 1,
                    'role'       => 'admin',
                    'created_at' => time(),
                    'updated_at' => time()
                ]
            ])
            ->getMockForAbstractClass();
        $result = $abstractMock->getCacheKeys();
        $this->assertNotEmpty($result);
        $this->assertSame($array, $result);
    }

    public function testReferenceCacheKeysNoCompanyId() {
        $array        = ['Member.by.company_id.', 'Member.id.1', 'Member.user_id.1'];
        $abstractMock = $this->getMockBuilder(Member::class)
            ->setMethods(null)
            ->setConstructorArgs([
                [
                    'id'         => 1,
                    'user_id'    => 1,
                    'role'       => 'admin',
                    'created_at' => time(),
                    'updated_at' => time()
                ]
            ])
            ->getMockForAbstractClass();
        $result = $abstractMock->getReferenceCacheKeys();
        $this->assertNotEmpty($result);
        $this->assertSame($array, $result);

    }

    public function testReferenceCacheKeysEmptyAttributes() {
        $array        = ['Member.by.company_id.', 'Member.id.', 'Member.user_id.'];
        $abstractMock = $this->getMockBuilder(Member::class)
            ->setMethods(null)
            ->setConstructorArgs([])
            ->getMockForAbstractClass();
        $result = $abstractMock->getReferenceCacheKeys();
        $this->assertNotEmpty($result);
        $this->assertSame($array, $result);

    }

    public function testReferenceCacheKeys() {
        $array        = ['Member.by.company_id.2', 'Member.id.1', 'Member.user_id.1'];
        $abstractMock = $this->getMockBuilder(Member::class)
            ->setMethods(null)
            ->setConstructorArgs([
                [
                    'id'         => 1,
                    'company_id' => 2,
                    'user_id'    => 1,
                    'role'       => 'admin',
                    'created_at' => time(),
                    'updated_at' => time()
                ]
            ])
            ->getMockForAbstractClass();
        $result = $abstractMock->getReferenceCacheKeys();
        $this->assertNotEmpty($result);
        $this->assertSame($array, $result);
    }
}
