<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace Test\Unit\Validator;

use App\Validator\Company\Credential;
use Respect\Validation\Exceptions\ExceptionInterface;
use Test\Unit\AbstractUnit;

class CredentialTest extends AbstractUnit {
    protected $validator;

    protected function setUp() {
        $this->validator = new Credential();
    }

    public function testAssertProductionEmpty() {
        $this->expectedException(ExceptionInterface::class);
        $this->validator->assertFlag([]);
    }

    public function testAssertProductionInvalid() {
        $this->expectedException(ExceptionInterface::class);
        $this->validator->assertFlag('dummy');
    }

    public function testProductionValueEmpty() {
        $this->assertFalse($this->validator->validateFlag([]));
    }

    public function testProductionValueInvalid() {
        $this->assertFalse($this->validator->validateFlag('dummy'));
    }

    public function testProductionValue() {
        $this->assertFalse($this->validator->validateFlag(false));
        $this->assertTrue($this->validator->validateFlag(true));
        $this->assertTrue($this->validator->validateFlag('true'));
        $this->assertTrue($this->validator->validateFlag(1));
        $this->assertFalse($this->validator->validateFlag(0));
    }
}
