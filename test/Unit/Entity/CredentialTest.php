<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace Test\Unit\Entity;

use App\Entity\Credential;
use Jenssegers\Optimus\Optimus;
use Test\Unit\AbstractUnit;

class CredentialTest extends AbstractUnit {
    /*
     * Jenssengers\Optimus\Optimus $optimus
     */
    private $optimus;

    public function setUp() {
        $this->optimus = $this->getMockBuilder(Optimus::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    private function getAttributes() {
        return [
            'id'          => 0,
            'name'        => 'My Credential',
            'public'      => 'public',
            'private'     => 'private',
            'production'  => false,
            'created_at'  => time(),
            'updated_at'  => time()
        ];
    }

    public function testSerialize() {
        $abstractMock = $this->getMockBuilder(Credential::class)
            ->setMethods(null)
            ->setConstructorArgs([array_merge(['company_id' => 0], $this->getAttributes()), $this->optimus])
            ->getMockForAbstractClass();
        $array = $abstractMock->serialize();
        $this->assertArrayHasKey('id', $array);
        $this->assertSame(0, $array['id']);
        $this->assertArrayHasKey('company_id', $array);
        $this->assertSame(0, $array['company_id']);
        $this->assertArrayHasKey('name', $array);
        $this->assertSame('My Credential', $array['name']);
        $this->assertArrayHasKey('slug', $array);
        $this->assertSame('my-credential', $array['slug']);
        $this->assertArrayHasKey('public', $array);
        $this->assertSame('public', $array['public']);
        $this->assertArrayHasKey('private', $array);
        $this->assertNotSame('private', $array['private']);
        $this->assertStringStartsWith('secure:', $array['private']);
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
        $abstractMock = $this->getMockBuilder(Credential::class)
            ->setMethods(null)
            ->setConstructorArgs([$this->getAttributes(), $this->optimus])
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

    public function testGetCachedKeysEmptyAttributes() {
        $array        = ['Credential.id.', 'Credential.slug.', 'Credential.public.'];
        $abstractMock = $this->getMockBuilder(Credential::class)
            ->setMethods(null)
            ->setConstructorArgs([[], $this->optimus])
            ->getMockForAbstractClass();
        $result = $abstractMock->getCacheKeys();
        $this->assertNotEmpty($result);
        $this->assertSame($array, $result);
    }

    public function testGetCachedKeys() {
        $array        = ['Credential.id.0', 'Credential.slug.my-credential', 'Credential.public.public'];
        $abstractMock = $this->getMockBuilder(Credential::class)
            ->setMethods(null)
            ->setConstructorArgs([$this->getAttributes(), $this->optimus])
            ->getMockForAbstractClass();
        $result = $abstractMock->getCacheKeys();
        $this->assertNotEmpty($result);
        $this->assertSame($array, $result);
    }

    public function testReferenceCacheKeysNoCompanyId() {
        $array        = ['Credential.by.company_id.', 'Credential.id.0', 'Credential.slug.my-credential', 'Credential.public.public'];
        $abstractMock = $this->getMockBuilder(Credential::class)
            ->setMethods(null)
            ->setConstructorArgs([$this->getAttributes(), $this->optimus])
            ->getMockForAbstractClass();
        $result = $abstractMock->getReferenceCacheKeys();
        $this->assertNotEmpty($result);
        $this->assertSame($array, $result);

    }

    public function testReferenceCacheKeysEmptyAttributes() {
        $array        = ['Credential.by.company_id.', 'Credential.id.', 'Credential.slug.', 'Credential.public.'];
        $abstractMock = $this->getMockBuilder(Credential::class)
            ->setMethods(null)
            ->setConstructorArgs([[], $this->optimus])
            ->getMockForAbstractClass();
        $result = $abstractMock->getReferenceCacheKeys();
        $this->assertNotEmpty($result);
        $this->assertSame($array, $result);

    }

    public function testReferenceCacheKeys() {
        $array        = ['Credential.by.company_id.0', 'Credential.id.0', 'Credential.slug.my-credential', 'Credential.public.public'];
        $abstractMock = $this->getMockBuilder(Credential::class)
            ->setMethods(null)
            ->setConstructorArgs(
                [
                    array_merge(
                        ['companyId' => '0'],
                        $this->getAttributes()
                    ),
                    $this->optimus
                ]
            )
            ->getMockForAbstractClass();
        $result = $abstractMock->getReferenceCacheKeys();
        $this->assertNotEmpty($result);
        $this->assertSame($array, $result);

    }
}
