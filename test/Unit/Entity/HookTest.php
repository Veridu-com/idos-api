<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace Test\Unit\Entity;

use App\Entity\Member;
use Jenssegers\Optimus\Optimus;
use Test\Unit\AbstractUnit;

class HookTest extends AbstractUnit {
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
        $array = [
            'id'            => 1,
            'credential_id' => 1,
            'trigger'       => 'trigger.test',
            'url'           => 'http://example.com/test.php',
            'subscribed'    => true,
            'created_at'    => time(),
            'updated_at'    => time()
        ];

        $abstractMock = $this->getMockBuilder(Hook::class)
            ->setMethods(null)
            ->setConstructorArgs([$array, $this->optimus])
            ->getMockForAbstractClass();
        $array = $abstractMock->serialize();
        $this->assertArrayHasKey('id', $array);
        $this->assertSame(1, $array['id']);
        $this->assertArrayHasKey('credential_id', $array);
        $this->assertSame(1, $array['credential_id']);
        $this->assertArrayHasKey('trigger', $array);
        $this->assertSame('trigger.test', $array['trigger']);
        $this->assertArrayHasKey('url', $array);
        $this->assertSame('http://example.com/test.php', $array['url']);
        $this->assertArrayHasKey('subscribed', $array);
        $this->assertSame(true, $array['subscribed']);
        $this->assertArrayHasKey('created_at', $array);
        $this->assertTrue(is_string($array['created_at']));
        $this->assertTrue(is_int($abstractMock->createdAt));
        $this->assertArrayHasKey('updated_at', $array);
        $this->assertTrue(is_string($array['updated_at']));
        $this->assertTrue(is_int($abstractMock->updatedAt));
    }

    public function testToArray() {
         $array = [
            'id'            => 1,
            'credential_id' => 1,
            'trigger'       => 'trigger.test',
            'url'           => 'http://example.com/test.php',
            'subscribed'    => true,
            'created_at'    => time(),
            'updated_at'    => time()
        ];
        $abstractMock = $this->getMockBuilder(Hook::class)
            ->setMethods(null)
            ->setConstructorArgs([$array, $this->optimus])
            ->getMockForAbstractClass();
        $array = $abstractMock->toArray();
        $this->assertArrayHasKey('id', $array);
        $this->assertSame(1, $array['id']);
        $this->assertArrayHasKey('credential_id', $array);
        $this->assertSame(1, $array['credential_id']);
        $this->assertArrayHasKey('trigger', $array);
        $this->assertSame('trigger.test', $array['trigger']);
        $this->assertArrayHasKey('url', $array);
        $this->assertSame('http://example.com/test.php', $array['url']);
        $this->assertArrayHasKey('subscribed', $array);
        $this->assertSame(true, $array['subscribed']);
        $this->assertArrayHasKey('created_at', $array);
        $this->assertTrue(is_string($array['created_at']));
        $this->assertTrue(is_int($abstractMock->createdAt));
        $this->assertArrayHasKey('updated_at', $array);
        $this->assertTrue(is_string($array['updated_at']));
        $this->assertTrue(is_int($abstractMock->updatedAt));
    }

    /*public function testGetCachedKeysEmptyAttributes() {
        $array        = ['Member.id.', 'Member.user_id.'];
        $abstractMock = $this->getMockBuilder(Member::class)
            ->setMethods(null)
            ->setConstructorArgs([[], $this->optimus])
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
                ],
                $this->optimus
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
                ],
                $this->optimus
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
            ->setConstructorArgs([[], $this->optimus])
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
                ],
                $this->optimus
            ])
            ->getMockForAbstractClass();
        $result = $abstractMock->getReferenceCacheKeys();
        $this->assertNotEmpty($result);
        $this->assertSame($array, $result);
    }*/
}
