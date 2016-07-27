<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace Test\Unit\Entity;

use App\Entity\Credential;
use Test\Unit\AbstractUnit;

class CredentialTest extends AbstractUnit {
    public function testSerialize() {
        $array = [
            'id'          => 0,
            'companyId'   => '0',
            'name'        => 'My Credential',
            'public'      => 'public',
            'private'     => 'private',
            'production'  => false,
            'created_at'  => time(),
            'updated_at'  => time()
        ];
        $abstractMock = $this->getMockBuilder(Credential::class)
            ->setMethods(null)
            ->setConstructorArgs(['attributes' => $array])
            ->getMockForAbstractClass();
        $array = $abstractMock->serialize();
        $this->assertArrayHasKey('id', $array);
        $this->assertSame(0, $array['id']);
        $this->assertArrayHasKey('company_id', $array);
        $this->assertSame('0', $array['company_id']);
        $this->assertArrayHasKey('name', $array);
        $this->assertSame('My Credential', $array['name']);
        $this->assertArrayHasKey('slug', $array);
        $this->assertSame('my-credential', $array['slug']);
        $this->assertArrayHasKey('public', $array);
        $this->assertSame('public', $array['public']);
        $this->assertArrayHasKey('private', $array);
        $this->assertSame('private', $array['private']);
        $this->assertArrayHasKey('production', $array);
        $this->assertFalse($array['production']);
        $this->assertArrayHasKey('created_at', $array);
        $this->assertTrue(is_string($array['created_at']));
        $this->assertTrue(is_int($abstractMock->createdAt));
        $this->assertArrayHasKey('updated_at', $array);
        $this->assertTrue(is_string($array['updated_at']));
        $this->assertTrue(is_int($abstractMock->updatedAt));
    }

    public function testToArray() {
        $array = [
            'id'          => 0,
            'companyId'   => '0',
            'name'        => 'My Credential',
            'public'      => 'public',
            'private'     => 'private',
            'production'  => false,
            'created_at'  => time(),
            'updated_at'  => time()
        ];
        $abstractMock = $this->getMockBuilder(Credential::class)
            ->setMethods(null)
            ->setConstructorArgs(['attributes' => $array])
            ->getMockForAbstractClass();
        $array = $abstractMock->toArray();
        $this->assertArrayHasKey('name', $array);
        $this->assertSame('My Credential', $array['name']);
        $this->assertArrayHasKey('slug', $array);
        $this->assertSame('my-credential', $array['slug']);
        $this->assertArrayHasKey('public', $array);
        $this->assertSame('public', $array['public']);
        $this->assertArrayHasKey('created_at', $array);
        $this->assertTrue(is_int($array['created_at']));
    }
}
