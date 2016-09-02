<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace Test\Unit\Entity;

use App\Entity\Feature;
use Jenssegers\Optimus\Optimus;
use Test\Unit\AbstractUnit;

class FeatureTest extends AbstractUnit {
    /*
     * Jenssengers\Optimus\Optimus $optimus
     */
    private $optimus;

    public function setUp() {
        $this->optimus = $this->getMockBuilder(Optimus::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->userId    = 1;
        $this->id        = 1;
        $this->name      = 'Test feature';
        $this->slug      = 'test-feature';
        $this->value     = 'testing this feature';
        $this->createdAt = time();
    }

    private function getAttributes() {
        return [
            'id'         => $this->id,
            'name'       => $this->name,
            'slug'       => $this->slug,
            'value'      => $this->value,
            'created_at' => $this->createdAt,
            'updated_at' => null
        ];
    }

    public function testSerialize() {
        $abstractMock = $this->getMockBuilder(Feature::class)
            ->setMethods(null)
            ->setConstructorArgs([array_merge(['userId' => $this->userId], $this->getAttributes()), $this->optimus])
            ->getMockForAbstractClass();

        $array = $abstractMock->serialize();

        $this->assertArrayHasKey('id', $array);
        $this->assertSame($this->id, $array['id']);

        $this->assertArrayHasKey('name', $array);
        $this->assertSame($this->name, $array['name']);

        $this->assertArrayHasKey('slug', $array);
        $this->assertSame($this->slug, $array['slug']);

        $this->assertArrayHasKey('value', $array);
        $this->assertNotSame($this->value, $array['value']);
        $this->assertStringStartsWith('secure:', $array['value']);

        $this->assertArrayHasKey('user_id', $array);
        $this->assertSame($this->userId, $array['user_id']);

        $this->assertArrayHasKey('created_at', $array);
        $this->assertTrue(is_string($array['created_at']));
        $this->assertTrue(is_int($abstractMock->createdAt));

        $this->assertArrayHasKey('updated_at', $array);
        $this->assertTrue(is_null($array['updated_at']));
        $this->assertTrue(is_null($abstractMock->updatedAt));
    }

    public function testToArray() {
        $abstractMock = $this->getMockBuilder(Feature::class)
            ->setMethods(null)
            ->setConstructorArgs([array_merge(['userId' => $this->userId], $this->getAttributes()), $this->optimus])
            ->getMockForAbstractClass();

        $array = $abstractMock->toArray();

        $this->assertArrayHasKey('name', $array);
        $this->assertSame($this->name, $array['name']);

        $this->assertArrayHasKey('slug', $array);
        $this->assertSame($this->slug, $array['slug']);

        $this->assertArrayHasKey('value', $array);
        $this->assertSame($this->value, $array['value']);

        $this->assertArrayHasKey('user_id', $array);
        $this->assertSame($this->userId, $array['user_id']);

        $this->assertArrayHasKey('created_at', $array);
        $this->assertTrue(is_int($array['created_at']));

        $this->assertArrayHasKey('updated_at', $array);
        $this->assertTrue(is_null($array['updated_at']));
    }

    public function testGetCachedKeysEmptyAttributes() {
        $array = ['Feature.id.', 'Feature.slug.'];

        $abstractMock = $this->getMockBuilder(Feature::class)
            ->setMethods(null)
            ->setConstructorArgs([[], $this->optimus])
            ->getMockForAbstractClass();

        $result = $abstractMock->getCacheKeys();

        $this->assertNotEmpty($result);
        // assertEquals: we want the array key => value combinations to be the same, but not necessarily in the same order
        $this->assertEquals($array, $result);
    }

    public function testGetCachedKeys() {
        $array = [sprintf('Feature.id.%s', $this->id), sprintf('Feature.slug.%s', $this->slug)];

        $abstractMock = $this->getMockBuilder(Feature::class)
            ->setMethods(null)
            ->setConstructorArgs(
                [
                    $this->getAttributes(),
                    $this->optimus
                ]
            )
            ->getMockForAbstractClass();
        $result = $abstractMock->getCacheKeys();

        $this->assertNotEmpty($result);
        // assertEquals: we want the array key => value combinations to be the same, but not necessarily in the same order
        $this->assertEquals($array, $result);
    }

    public function testReferenceCacheKeysNoUserId() {
        $array = ['Feature.by.user_id.', sprintf('Feature.id.%s', $this->id), sprintf('Feature.slug.%s', $this->slug)];

        $abstractMock = $this->getMockBuilder(Feature::class)
            ->setMethods(null)
            ->setConstructorArgs([$this->getAttributes(), $this->optimus])
            ->getMockForAbstractClass();

        $result = $abstractMock->getReferenceCacheKeys();

        $this->assertNotEmpty($result);
        // assertEquals: we want the array key => value combinations to be the same, but not necessarily in the same order
        $this->assertEquals($array, $result);
    }

    public function testReferenceCacheKeysEmptyAttributes() {
        $array = ['Feature.by.user_id.', 'Feature.id.', 'Feature.slug.'];

        $abstractMock = $this->getMockBuilder(Feature::class)
            ->setMethods(null)
            ->setConstructorArgs([[], $this->optimus])
            ->getMockForAbstractClass();

        $result = $abstractMock->getReferenceCacheKeys();

        $this->assertNotEmpty($result);
        // assertEquals: we want the array key => value combinations to be the same, but not necessarily in the same order
        $this->assertEquals($array, $result);
    }

    public function testReferenceCacheKeys() {
        $array = [sprintf('Feature.by.user_id.%s', $this->userId), sprintf('Feature.id.%s', $this->id), sprintf('Feature.slug.%s', $this->slug)];

        $abstractMock = $this->getMockBuilder(Feature::class)
            ->setMethods(null)
            ->setConstructorArgs([array_merge(['userId' => $this->userId], $this->getAttributes()), $this->optimus])
            ->getMockForAbstractClass();

        $result = $abstractMock->getReferenceCacheKeys();

        $this->assertNotEmpty($result);
        // assertEquals: we want the array key => value combinations to be the same, but not necessarily in the same order
        $this->assertEquals($array, $result);
    }
}
