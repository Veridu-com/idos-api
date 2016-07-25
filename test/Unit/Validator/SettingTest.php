<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace Test\Unit\Validator;

use App\Validator\Setting;
use Respect\Validation\Exceptions\ExceptionInterface;
use Respect\Validation\Validator;
use Test\Unit\AbstractUnit;

class SettingTest extends AbstractUnit {
    protected $validator;

    protected function setUp() {
        $this->validator = new Setting();
    }

    public function testAssertPropNameEmpty() {
        $this->setExpectedException(ExceptionInterface::class);
        $this->validator->assertPropName('');
    }

    public function testAssertSectionNameEmpty() {
        $this->setExpectedException(ExceptionInterface::class);
        $this->validator->assertSectionName('');
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
}
