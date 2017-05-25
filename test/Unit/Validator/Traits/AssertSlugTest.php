<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace Test\Unit\Validator\Traits;

use App\Validator\Traits\AssertSlug;
use Respect\Validation\Exceptions\ExceptionInterface;
use Test\Unit\AbstractUnit;

class AssertSlugTest extends AbstractUnit {
    public function testAssertSlugEmpty() {
        $this->expectedException(ExceptionInterface::class);

        $traitMock = $this->getMockForTrait(AssertSlug::class);
        $traitMock->assertSlug('');
    }

    public function testAssertSlugOneChar() {
        $traitMock  = $this->getMockForTrait(AssertSlug::class);
        $validChars = [];
        foreach (range('a', 'z') as $char) {
            $validChars[] = $char;
        }

        foreach (range(0, 9) as $char) {
            $validChars[] = $char;
        }

        $probeString = [];
        $countChars  = count($validChars);
        for ($i = 0; $i < $countChars; $i++) {
            $probeString[($i % 15)] = $validChars[$i];
            $traitMock->assertSlug(implode('', $probeString));
        }

        $this->assertTrue(true);
    }

    public function testAssertSlugValid() {
        $traitMock = $this->getMockForTrait(AssertSlug::class);
        $traitMock->assertSlug('my-slug-here');
        $this->assertTrue(true);
    }

    public function testAssertSlugFifteenChars() {
        $traitMock = $this->getMockForTrait(AssertSlug::class);
        $traitMock->assertSlug(str_repeat('a', 15));
        $this->assertTrue(true);
    }

    public function testAssertSlugSixteenChars() {
        $this->expectedException(ExceptionInterface::class);

        $traitMock = $this->getMockForTrait(AssertSlug::class);
        $traitMock->assertSlug(str_repeat('a', 16));
    }

    public function testAssertSlugInvalidInput() {
        $this->expectedException(ExceptionInterface::class);

        $traitMock = $this->getMockForTrait(AssertSlug::class);
        $traitMock->assertSlug('Invalid Slug');
    }
}
