<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace Test\Unit\Validator\Traits;

use App\Validator\Traits\AssertId;
use Respect\Validation\Exceptions\ExceptionInterface;
use Test\Unit\AbstractUnit;

class AssertIdTest extends AbstractUnit {
    public function testAssertIdEmpty() {
        $this->expectedException(ExceptionInterface::class);

        $traitMock = $this->getMockForTrait(AssertId::class);
        $traitMock->assertId('');
    }

    public function testAssertIdInvalidValue() {
        $this->expectedException(Exceptioninterface::class);

        $traitMock = $this->getMockForTrait(AssertId::class);
        $traitMock->assertId('0a');
    }

    public function testAssertIdInvalidNumberWithString() {
        $this->expectedException(ExceptionInterface::class);

        $traitMock = $this->getMockForTrait(AssertId::class);
        $traitMock->assertId('dummy');
    }

    public function testAssertIdInvalidNumberWithBoolean() {
        $this->expectedException(ExceptionInterface::class);

        $traitMock = $this->getMockForTrait(AssertId::class);
        $traitMock->assertId(false);
    }

    public function testAssertIdNegativeValue() {
        $this->expectedException(Exceptioninterface::class);

        $traitMock = $this->getMockForTrait(AssertId::class);
        $traitMock->assertId(-1);
    }

    public function testAssertIdValid() {
        $traitMock = $this->getMockForTrait(AssertId::class);
        $traitMock->assertId(1);

        $this->assertTrue(true);
    }
}
