<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace Test\Unit\Entity;

use App\Entity\Service;
use Test\Unit\AbstractUnit;

class ServiceTest extends AbstractUnit {
    public function testSerialize() {

        $updated = time();
        $array   = [
            'id'          => 1,
            'name'        => 'My Service',
            'enabled'  => true,
            'created_at'  => time(),
            'updated_at'  => time()
        ];

        $abstractMock = $this->getMockBuilder(Service::class)
            ->setMethods(null)
            ->setConstructorArgs([$array])
            ->getMockForAbstractClass();
        $array = $abstractMock->serialize();
        $this->assertArrayHasKey('id', $array);
        $this->assertSame(1, $array['id']);
        $this->assertArrayHasKey('name', $array);
        $this->assertSame('My Service', $array['name']);
        $this->assertArrayHasKey('slug', $array);
        $this->assertSame('my-service', $array['slug']);
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
        $abstractMock = $this->getMockBuilder(Service::class)
            ->setMethods(null)
            ->setConstructorArgs(
                [
                    [
                        'id'          => 1,
                        'name'        => 'My Service',
                        'enabled'  => true,
                        'created_at'  => time(),
                        'updated_at'  => time()
                    ]
                ]
            )
            ->getMockForAbstractClass();

        $array = $abstractMock->toArray();
        $this->assertArrayHasKey('name', $array);
        $this->assertSame('My Service', $array['name']);
        $this->assertArrayHasKey('slug', $array);
        $this->assertSame('my-service', $array['slug']);
        $this->assertArrayHasKey('enabled', $array);
        $this->assertTrue($array['enabled']);
        $this->assertArrayHasKey('created_at', $array);
        $this->assertTrue(is_int($array['created_at']));
    }
}
