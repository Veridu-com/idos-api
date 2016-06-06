<?php
/**
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace Test\Unit\Validator;

use App\Validator\Company;
use Respect\Validation\Exceptions\ExceptionInterface;

class CompanyTest extends \PHPUnit_Framework_TestCase {
    protected $validator;

    protected function setUp() {
        $this->validator = new Company;
    }

    public function testAssertIdEmpty() {
        $this->setExpectedException(ExceptionInterface::class);
        $this->validator->assertId('');
    }

    public function testAssertIdInvalidValue() {
        $this->setExpectedException(Exceptioninterface::class);
        $this->validator->assertId('0a');
    }

    public function testAssertId() {
        $this->validator->assertId(1);
        $this->assertTrue(true);
    }

    public function testAssertNameEmpty() {
        $this->setExpectedException(ExceptionInterface::class);
        $this->validator->assertName('');
    }

    public function testAssertNameOneChar() {
        $this->validator->assertName('a');
        $this->assertTrue(true);
    }

    public function testAssertNameFifteenChars() {
        $this->validator->assertName('aaaaaaaaaaaaaaa');
        $this->assertTrue(true);
    }

    public function testAssertNameSixteenChars() {
        $this->setExpectedException(ExceptionInterface::class);
        $this->validator->assertName('aaaaaaaaaaaaaaaa');
    }

    public function testAssertNameInvalidInput() {
        $this->setExpectedException(ExceptionInterface::class);
        $this->validator->assertName(chr(20) . chr(127));
    }

    public function testAssertSlugEmpty() {
        $this->setExpectedException(ExceptionInterface::class);
        $this->validator->assertSlug('');
    }

    public function testAssertSlugOneChar() {
        $this->validator->assertSlug('a');
        $this->assertTrue(true);
    }

    public function testAssertSlugFifteenChars() {
        $this->validator->assertSlug('aaaaaaaaaaaaaaa');
        $this->assertTrue(true);
    }

    public function testAssertSlugSixteenChars() {
        $this->setExpectedException(ExceptionInterface::class);
        $this->validator->assertSlug('aaaaaaaaaaaaaaaa');
    }

    public function testAssertSlugInvalidInput() {
        $this->setExpectedException(ExceptionInterface::class);
        $this->validator->assertSlug('flávio');
    }

    public function testAssertParentIdEmpty() {
        $this->setExpectedException(ExceptionInterface::class);
        $this->validator->assertParentId('');
    }

    public function testAssertParentId() {
        $this->validator->assertParentId(null);
        $this->validator->assertParentId(1);
        $this->assertTrue(true);
    }

    public function testAssertParentIdInvalidValue() {
        $this->setExpectedException(ExceptionInterface::class);
        $this->validator->assertParentId('0a');
    }
}
