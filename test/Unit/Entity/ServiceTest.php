<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace Test\Unit\Entity;

use App\Entity\Handler;
use Jenssegers\Optimus\Optimus;
use Test\Unit\AbstractUnit;

class ServiceTest extends AbstractUnit {
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
            'id'         => 1,
            'name'       => 'My Handler',
            'enabled'    => true,
            'created_at' => time(),
            'updated_at' => time()
        ];

        $abstractMock = $this->getMockBuilder(Handler::class)
            ->setMethods(null)
            ->setConstructorArgs([$array, $this->optimus])
            ->getMockForAbstractClass();
        $array = $abstractMock->serialize();
        $this->assertArrayHasKey('id', $array);
        $this->assertSame(1, $array['id']);
        $this->assertArrayHasKey('name', $array);
        $this->assertSame('My Handler', $array['name']);
        $this->assertArrayHasKey('enabled', $array);
        $this->assertTrue($array['enabled']);
        $this->assertArrayHasKey('created_at', $array);
        $this->assertTrue(is_string($array['created_at']));
        $this->assertTrue(is_int($abstractMock->createdAt));
        $this->assertArrayHasKey('updated_at', $array);
        $this->assertTrue(is_string($array['updated_at']));
        $this->assertTrue(is_int($abstractMock->updatedAt));
    }

    public function testToArray() {
        $abstractMock = $this->getMockBuilder(Handler::class)
            ->setMethods(null)
            ->setConstructorArgs(
                [
                    [
                        'id'         => 1,
                        'name'       => 'My Handler',
                        'url'        => 'url',
                        'access'     => 0x01,
                        'enabled'    => true,
                        'created_at' => time(),
                        'updated_at' => time()
                    ],
                    $this->optimus
                ]
            )
            ->getMockForAbstractClass();

        $array = $abstractMock->toArray();

        $this->assertArrayHasKey('name', $array);
        $this->assertSame('My Handler', $array['name']);

        $this->assertArrayHasKey('enabled', $array);
        $this->assertTrue($array['enabled']);

        $this->assertArrayHasKey('access', $array);
        $this->assertEquals($array['access'], 0x01);

        $this->assertArrayHasKey('created_at', $array);
        $this->assertTrue(is_int($array['created_at']));
    }
}
