<?php
/**
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace Test\Unit\Entity;

use Test\Unit\AbstractUnit;
use App\Entity\Company;

class CompanyTest extends \PHPUnit_Framework_TestCase {
    public function testSerialize() {
        $array = [
            'id'          => 0,
            'name'        => 'My Co',
            'public_key'  => 'pkey',
            'private_key' => 'privkey',
            'created_at'  => time(),
            'updated_at'  => time()
        ];
        $abstractMock = $this->getMockBuilder(Company::class)
            ->setMethods(null)
            ->setConstructorArgs(['attributes' => $array])
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
        $this->assertSame('privkey', $array['private_key']);
        $this->assertArrayHasKey('created_at', $array);
        $this->assertTrue(is_int($array['created_at']));
        $this->assertArrayHasKey('updated_at', $array);
        $this->assertTrue(is_int($array['updated_at']));
    }

    public function testToArray() {
        $abstractMock = $this->getMockBuilder(Company::class)
            ->setMethods(null)
            ->setConstructorArgs(
                [
                    'attributes' => [
                        'id'          => 0,
                        'name'        => 'My Co',
                        'public_key'  => 'pkey',
                        'private_key' => 'privkey',
                        'created_at'  => time(),
                        'updated_at'  => time()
                    ]
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
}
