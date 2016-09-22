<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace Test\Unit\Validator;

use App\Validator\Company\Member;
use Respect\Validation\Exceptions\ExceptionInterface;
use Test\Unit\AbstractUnit;

class MemberTest extends AbstractUnit {
    protected $validator;

    protected function setUp() {
        $this->validator = new Member();
    }

    public function testAssertUserNameFiftyChars() {
        $username = '';
        for ($i = 0; $i < 50; $i++) {
            $username .= 'a';
        }

        $this->validator->assertUserName($username);
        $this->assertTrue(true);
    }

    public function testAssertNameFiftyOneChars() {
        $this->setExpectedException(ExceptionInterface::class);
        $username = '';
        for ($i = 0; $i < 51; $i++) {
            $username .= 'a';
        }

        $this->validator->assertUserName($username);
    }

    public function testAssertNameInvalidInput() {
        $this->setExpectedException(ExceptionInterface::class);
        $this->validator->assertUserName(chr(20) . chr(127));
    }
}
