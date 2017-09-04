<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace Test\Unit\Factory;

use App\Entity\Company;
use App\Factory\Entity;
use Jenssegers\Optimus\Optimus;
use Test\Unit\AbstractUnit;

class EntityClass extends AbstractUnit {
    /*
     * Jenssengers\Optimus\Optimus $optimus
     */
    private $optimus;

    public function setUp() {
        $this->optimus = $this->getMockBuilder(Optimus::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testCreateNotFound() {
        $entity = new Entity($this->optimus);
        $this->expectedException(\RuntimeException::class);
        $entity->create('Dummy', []);
    }

    public function testCreateEmptyAttributes() {
        $array = [
            'name'       => null,
            'slug'       => null,
            'public_key' => null,
            'created_at' => null,
            'updated_at' => null
        ];

        $entity  = new Entity($this->optimus);
        $company = $entity->create('company', []);
        $this->assertInstanceOf(Company::class, $company);
        // assertEquals: we want the array key => value combinations to be the same, but not necessarily in the same order
        $this->assertEquals($array, $company->toArray());
    }

    public function testCreateWithAttributes() {
        $array = [
            'name'       => 'New Company',
            'slug'       => 'new-company',
            'public_key' => 'pub_key',
            'created_at' => time(),
            'updated_at' => time(),
        ];

        $entity  = new Entity($this->optimus);
        $company = $entity->create('company', $array);
        $this->assertInstanceOf(Company::class, $company);
        // assertEquals: we want the array key => value combinations to be the same, but not necessarily in the same order
        $this->assertEquals($array, $company->toArray());
    }
}
