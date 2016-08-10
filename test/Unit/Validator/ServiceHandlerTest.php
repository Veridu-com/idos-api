<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace Test\Unit\Validator;

use App\Validator\ServiceHandler;
use Respect\Validation\Exceptions\ExceptionInterface;
use Test\Unit\AbstractUnit;

class ServiceHandlerTest extends AbstractUnit {
    protected $validator;

    protected function setUp() {
        $this->validator = new ServiceHandler();
    }

    public function testAssertSourceInvalid() {
        $this->setExpectedException(ExceptionInterface::class);
        $this->validator->assertName([]);
    }

    public function testAssertSource() {
        $this->validator->assertSource('source');
        $this->assertTrue(true);
    }

    public function testAssertSlugInvalid() {
        $this->setExpectedException(ExceptionInterface::class);
        $this->validator->assertName([]);
    }

    public function testAssertSlug() {
        $this->validator->assertSlug('slug');
        $this->assertTrue(true);
    }

    public function testAssertLocationInvalidUrl() {
        $this->setExpectedException(ExceptionInterface::class);
        $this->validator->assertLocation('url');
    }

    public function testAssertLocation() {
        $this->validator->assertLocation('http://localhost:8080');
        $this->assertTrue(true);
    }

    public function testAuthUsernameInvalid() {
        $this->setExpectedException(ExceptionInterface::class);
        $this->validator->assertAuthUsername([]);
    }

    public function testAuthUsername() {
        $this->validator->assertAuthUsername('Auth Username');
        $this->assertTrue(true);
    }

    public function testAuthPasswordInvalid() {
        $this->setExpectedException(ExceptionInterface::class);
        $this->validator->assertAuthPassword([]);
    }

    public function testAuthPassword() {
        $this->validator->assertAuthPassword('Auth Password');
        $this->assertTrue(true);
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
}
