<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace Test\Unit\Entity;

use App\Entity\Mapped;
use Jenssegers\Optimus\Optimus;
use Test\Unit\AbstractUnit;

class MappedTest extends AbstractUnit {
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
            'source_id'  => 1,
            'name'       => 'mapped-test',
            'value'      => 'value-test',
            'created_at' => time()
        ];

        $abstractMock = $this->getMockBuilder(Mapped::class)
            ->setMethods(null)
            ->setConstructorArgs([$array, $this->optimus])
            ->getMockForAbstractClass();
        $array = $abstractMock->serialize();

        $this->assertArrayHasKey('id', $array);
        $this->assertSame(1, $array['id']);

        $this->assertArrayHasKey('source_id', $array);
        $this->assertSame(1, $array['source_id']);

        $this->assertArrayHasKey('name', $array);
        $this->assertSame('mapped-test', $array['name']);

        $this->assertArrayHasKey('value', $array);
        $this->assertNotSame('value-test', $array['value']);
        $this->assertStringStartsWith('secure:', $array['value']);

        $this->assertArrayHasKey('created_at', $array);
        $this->assertTrue(is_string($array['created_at']));
        $this->assertTrue(is_int($abstractMock->createdAt));
    }

    public function testToArray() {
        $abstractMock = $this->getMockBuilder(Mapped::class)
            ->setMethods(null)
            ->setConstructorArgs(
                [
                    [
                        'id'         => 1,
                        'source_id'  => 1,
                        'name'       => 'mapped-test',
                        'value'      => 'value-test',
                        'created_at' => time()
                    ],
                    $this->optimus
                ]
            )
            ->getMockForAbstractClass();

        $array = $abstractMock->toArray();
        $this->assertArrayHasKey('name', $array);
        $this->assertSame('mapped-test', $array['name']);

        $this->assertArrayHasKey('value', $array);
        $this->assertSame('value-test', $array['value']);

        $this->assertArrayHasKey('created_at', $array);
        $this->assertTrue(is_int($array['created_at']));
    }
}
