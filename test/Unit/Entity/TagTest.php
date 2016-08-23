<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace Test\Unit\Entity;

use App\Entity\Tag;
use Jenssegers\Optimus\Optimus;
use Test\Unit\AbstractUnit;

class TagTest extends AbstractUnit {
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
            'id'         => 1,
            'user_id'    => 1,
            'name'       => 'Test Tag',
            'slug'       => 'test-tag',
            'created_at' => time(),
            'updated_at' => time()
        ];

        $abstractMock = $this->getMockBuilder(Tag::class)
            ->setMethods(null)
            ->setConstructorArgs([$array, $this->optimus])
            ->getMockForAbstractClass();
        $array = $abstractMock->serialize();
        $this->assertArrayHasKey('id', $array);
        $this->assertSame(1, $array['id']);
        $this->assertArrayHasKey('user_id', $array);
        $this->assertSame(1, $array['user_id']);
        $this->assertArrayHasKey('name', $array);
        $this->assertArrayHasKey('slug', $array);
        $this->assertSame('Test Tag', $array['name']);
        $this->assertSame('test-tag', $array['slug']);
        $this->assertArrayHasKey('created_at', $array);
        $this->assertTrue(is_string($array['created_at']));
        $this->assertTrue(is_int($abstractMock->createdAt));
        $this->assertArrayHasKey('updated_at', $array);
        $this->assertTrue(is_string($array['updated_at']));
        $this->assertTrue(is_int($abstractMock->updatedAt));
    }

    public function testToArray() {
         $array = [
            'id'         => 1,
            'user_id'    => 1,
            'name'       => 'Test Tag',
            'slug'       => 'test-tag',
            'created_at' => time(),
            'updated_at' => time()
        ];
        $abstractMock = $this->getMockBuilder(Tag::class)
            ->setMethods(null)
            ->setConstructorArgs([$array, $this->optimus])
            ->getMockForAbstractClass();
        $array = $abstractMock->toArray();
        $this->assertArrayHasKey('name', $array);
        $this->assertArrayHasKey('slug', $array);
        $this->assertSame('Test Tag', $array['name']);
        $this->assertSame('test-tag', $array['slug']);
        $this->assertArrayHasKey('created_at', $array);
        $this->assertTrue(is_int($abstractMock->createdAt));
    }
}
