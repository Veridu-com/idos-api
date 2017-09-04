<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace Test\Unit\Entity;

use App\Entity\Company\Setting;
use Jenssegers\Optimus\Optimus;
use Test\Unit\AbstractUnit;

class SettingTest extends AbstractUnit {
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
            'id'         => 1,
            'section'    => 'A Section',
            'property'   => 'property',
            'value'      => 'value',
            'created_at' => time(),
            'updated_at' => time()
        ];
    }

    public function testSerialize() {
        $abstractMock = $this->getMockBuilder(Setting::class)
            ->setMethods(null)
            ->setConstructorArgs([array_merge(['companyId' => 0], $this->getAttributes()), $this->optimus])
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
        $this->assertNotSame('value', $array['value']);
        $this->assertStringStartsWith('secure:', $array['value']);
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
            ->setConstructorArgs([$this->getAttributes(), $this->optimus])
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

    public function testGetCachedKeysEmptyAttributes() {
        $array        = ['Setting.company_id..section..property.'];
        $abstractMock = $this->getMockBuilder(Setting::class)
            ->setMethods(null)
            ->setConstructorArgs([[], $this->optimus])
            ->getMockForAbstractClass();
        $result = $abstractMock->getCacheKeys();
        $this->assertNotEmpty($result);
        // assertEquals: we want the array key => value combinations to be the same, but not necessarily in the same order
        $this->assertEquals($array, $result);
    }

    public function testGetCachedKeys() {
        $array        = ['Setting.company_id.0.section.A Section.property.property'];
        $abstractMock = $this->getMockBuilder(Setting::class)
            ->setMethods(null)
            ->setConstructorArgs([array_merge(['companyId' => 0], $this->getAttributes()), $this->optimus])
            ->getMockForAbstractClass();
        $result = $abstractMock->getCacheKeys();
        $this->assertNotEmpty($result);
        // assertEquals: we want the array key => value combinations to be the same, but not necessarily in the same order
        $this->assertEquals($array, $result);
    }

    public function testReferenceCacheKeysNoCompanyId() {
        $array        = ['Setting.by.company_id.', 'Setting.by.company_id..section.A Section', 'Setting.company_id..section.A Section.property.property'];
        $abstractMock = $this->getMockBuilder(Setting::class)
            ->setMethods(null)
            ->setConstructorArgs([$this->getAttributes(), $this->optimus])
            ->getMockForAbstractClass();
        $result = $abstractMock->getReferenceCacheKeys();
        $this->assertNotEmpty($result);
        // assertEquals: we want the array key => value combinations to be the same, but not necessarily in the same order
        $this->assertEquals($array, $result);
    }

    public function testReferenceCacheKeysEmptyAttributes() {
        $array        = ['Setting.by.company_id.', 'Setting.by.company_id..section.', 'Setting.company_id..section..property.'];
        $abstractMock = $this->getMockBuilder(Setting::class)
            ->setMethods(null)
            ->setConstructorArgs([[], $this->optimus])
            ->getMockForAbstractClass();
        $result = $abstractMock->getReferenceCacheKeys();
        $this->assertNotEmpty($result);
        // assertEquals: we want the array key => value combinations to be the same, but not necessarily in the same order
        $this->assertEquals($array, $result);
    }

    public function testReferenceCacheKeys() {
        $array        = ['Setting.by.company_id.0', 'Setting.by.company_id.0.section.A Section',  'Setting.company_id.0.section.A Section.property.property'];
        $abstractMock = $this->getMockBuilder(Setting::class)
            ->setMethods(null)
            ->setConstructorArgs([array_merge(['companyId' => 0], $this->getAttributes()), $this->optimus])
            ->getMockForAbstractClass();
        $result = $abstractMock->getReferenceCacheKeys();
        $this->assertNotEmpty($result);
        // assertEquals: we want the array key => value combinations to be the same, but not necessarily in the same order
        $this->assertEquals($array, $result);
    }
}
