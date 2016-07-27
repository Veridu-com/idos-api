<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace Test\Unit\Entity;

use App\Entity\Setting;
use Test\Unit\AbstractUnit;

class SettingTest extends AbstractUnit {
    public function testSerialize() {
        $updated = time();
        $array   = [
            'id'             => 1,
            'companyId'      => 0,
            'section'        => 'A Section',
            'property'       => 'property',
            'value'          => 'value',
            'created_at'     => time(),
            'updated_at'     => $updated
        ];

        $abstractMock = $this->getMockBuilder(Setting::class)
            ->setMethods(null)
            ->setConstructorArgs(['attributes' => $array])
            ->getMockForAbstractClass();

        $array = $abstractMock->serialize();
        $this->assertArrayHasKey('id', $array);
        $this->assertSame(1, $array['id']);
        $this->assertArrayHasKey('company_id', $array);
        $this->assertSame(0, $array['company_id']);
        $this->assertArrayHasKey('section', $array);
        $this->assertSame('A Section', $array['section']);
        $this->assertArrayHasKey('property', $array);
        $this->assertSame('property', $array['property']);
        $this->assertArrayHasKey('value', $array);
        $this->assertSame('value', $array['value']);
        $this->assertArrayHasKey('created_at', $array);
        $this->assertTrue(is_string($array['created_at']));
        $this->assertTrue(is_int($abstractMock->createdAt));
        $this->assertArrayHasKey('updated_at', $array);
        $this->assertTrue(is_string($array['updated_at']));
        $this->assertTrue(is_int($abstractMock->updatedAt));
    }

    public function testToArray() {
        $abstractMock = $this->getMockBuilder(Setting::class)
            ->setMethods(null)
            ->setConstructorArgs(
                [
                    'attributes' => [
                    'id'             => 1,
                    'companyId'      => 0,
                    'section'        => 'A Section',
                    'property'       => 'property',
                    'value'          => 'value',
                    'created_at'     => time(),
                    'updated_at'     => time()
                    ]
                ]
            )
            ->getMockForAbstractClass();

        $array = $abstractMock->toArray();
        $this->assertArrayHasKey('section', $array);
        $this->assertSame('A Section', $array['section']);
        $this->assertArrayHasKey('property', $array);
        $this->assertSame('property', $array['property']);
        $this->assertArrayHasKey('value', $array);
        $this->assertSame('value', $array['value']);
        $this->assertArrayHasKey('created_at', $array);
        $this->assertTrue(is_int($array['created_at']));
    }
}
