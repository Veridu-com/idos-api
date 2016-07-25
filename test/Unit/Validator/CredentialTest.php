<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace Test\Unit\Validator;

use App\Validator\Credential;
use Respect\Validation\Exceptions\ExceptionInterface;
use Respect\Validation\Validator;
use Test\Unit\AbstractUnit;

class CredentialTest extends AbstractUnit {
    protected $validator;

    protected function setUp() {
        $this->validator = new Credential();
    }

    public function testAssertNameEmpty() {
        $this->setExpectedException(ExceptionInterface::class);
        $this->validator->assertName('');
    }

    public function testAssertNameExcededCharacters() {
        $this->setExpectedException(ExceptionInterface::class);
        $this->validator->assertName('abcdefghijklmnopqrstuv');
    }

    public function testAssertSlugEmpty() {
        $this->setExpectedException(ExceptionInterface::class);
        $this->validator->assertSlug('');
    }

    public function testAssertSlugInvalid() {
        $this->setExpectedException(ExceptionInterface::class);
        $this->validator->assertSlug('abcdefghijklmnopqrstuv');
    }

    public function testAssertProductionEmpty() {
        $this->setExpectedException(ExceptionInterface::class);
        $this->validator->assertProduction([]);
    }

    public function testAssertProductionInvalid() {
        $this->setExpectedException(ExceptionInterface::class);
        $this->validator->assertProduction('dummy');
    }

    public function testAssertCompanyIdEmpty() {
        $this->setExpectedException(ExceptionInterface::class);
        $this->validator->assertCompanyId([]);
    }

    public function testAssertCompanyIdInvalidNumberWithString() {
        $this->setExpectedException(ExceptionInterface::class);
        $this->validator->assertCompanyId('dummy');
    }

    public function testAssertCompanyIdInvalidNumberWithBoolean() {
        $this->setExpectedException(ExceptionInterface::class);
        $this->validator->assertCompanyId(false);
    }

    public function testAssertIdEmpty() {
        $this->setExpectedException(ExceptionInterface::class);
        $this->validator->assertId([]);
    }

    public function testAssertIdInvalidNumberWithString() {
        $this->setExpectedException(ExceptionInterface::class);
        $this->validator->assertId('dummy');
    }

    public function testAssertIdInvalidNumberWithBoolean() {
        $this->setExpectedException(ExceptionInterface::class);
        $this->validator->assertId(false);
    }

    public function testProductionValueEmpty() {
        $this->assertFalse($this->validator->productionValue([]));
    }

    public function testProductionValueInvalid() {
        $this->assertFalse($this->validator->productionValue('dummy'));
    }

    public function testProductionValue() {
        $this->assertFalse($this->validator->productionValue(false));
        $this->assertTrue($this->validator->productionValue(true));
        $this->assertTrue($this->validator->productionValue('true'));
        $this->assertTrue($this->validator->productionValue(1));
        $this->assertFalse($this->validator->productionValue(0));
    }
}
