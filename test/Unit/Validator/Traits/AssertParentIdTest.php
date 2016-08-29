<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace Test\Unit\Validator\Traits;

use App\Validator\Traits\AssertParentId;
use Respect\Validation\Exceptions\ExceptionInterface;
use Test\Unit\AbstractUnit;

class AssertParentIdTest extends AbstractUnit {
    public function testAssertParentIdEmpty() {
        $this->setExpectedException(ExceptionInterface::class);

        $traitMock = $this->getMockForTrait(AssertParentId::class);
        $traitMock->assertParentId('');
    }

    public function testAssertParentIdInvalidValue() {
        $this->setExpectedException(Exceptioninterface::class);

        $traitMock = $this->getMockForTrait(AssertParentId::class);
        $traitMock->assertParentId('0a');
    }

    public function testAssertParentIdInvalidNumberWithString() {
        $this->setExpectedException(ExceptionInterface::class);

        $traitMock = $this->getMockForTrait(AssertParentId::class);
        $traitMock->assertParentId('dummy');
    }

    public function testAssertParentIdInvalidNumberWithBoolean() {
        $this->setExpectedException(ExceptionInterface::class);

        $traitMock = $this->getMockForTrait(AssertParentId::class);
        $traitMock->assertParentId(false);
    }

    public function testAssertParentIdNegativeValue() {
        $this->setExpectedException(Exceptioninterface::class);

        $traitMock = $this->getMockForTrait(AssertParentId::class);
        $traitMock->assertParentId(-1);
    }

    public function testAssertParentIdValid() {
        $traitMock = $this->getMockForTrait(AssertParentId::class);
        $traitMock->assertParentId(null);
        $traitMock->assertParentId(1);

        $this->assertTrue(true);
    }
}
