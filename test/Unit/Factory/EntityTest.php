<?php

declare (strict_types = 1);

use App\Entity\Company;
use App\Factory\Entity;
use Test\Unit\AbstractUnit;

class EntityClass extends AbstractUnit {
    public function testCreateNotFound() {
        $entity = new Entity();
        $this->setExpectedException(\RuntimeException::class);
        $entity->create('Dummy', []);
    }

    public function testCreateEmptyAttributes() {
        $array = [
            'name'       => null,
            'slug'       => null,
            'public_key' => null,
            'created_at' => null,
            'updated_at' => null,
        ];

        $entity  = new Entity();
        $company = $entity->create('company', []);
        $this->assertInstanceOf(Company::class, $company);
        $this->assertSame($array, $company->toArray());
    }

    public function testCreateWithAttributes() {
        $array = [
            'name'       => 'New Company',
            'slug'       => 'new-company',
            'public_key' => 'pub_key',
            'created_at' => time(),
            'updated_at' => time(),
        ];

        $entity  = new Entity();
        $company = $entity->create('company', $array);
        $this->assertInstanceOf(Company::class, $company);
        $this->assertSame($array, $company->toArray());
    }
}
