<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace Test\Unit\Entity;

use App\Entity\Company;
use Test\Unit\AbstractUnit;

class CompanyTest extends AbstractUnit {
    public function testSerialize() {

        $created = time();
        $updated = time();
        $array   = [
            'id'          => 0,
            'name'        => 'My Co',
            'public_key'  => 'pkey',
            'private_key' => 'privkey',
            'created_at'  => $created,
            'updated_at'  => $updated
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
        $this->assertSame($created, strtotime($array['created_at']));
        $this->assertArrayHasKey('updated_at', $array);
        $this->assertSame($updated, strtotime($array['updated_at']));
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
