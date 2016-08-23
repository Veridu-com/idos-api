<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace Test\Unit\Validator;

use App\Validator\Tag;
use Respect\Validation\Exceptions\ExceptionInterface;
use Test\Unit\AbstractUnit;

class TagTest extends AbstractUnit {
    protected $validator;

    protected function setUp() {
        $this->validator = new Tag();
    }

    public function testAssertId() {
        $this->validator->assertId(1);
        $this->assertTrue(true);
    }

    public function testAssertNameFiftyChars() {
        $username = '';
        for ($i = 0; $i < 50; $i++) {
            $username .= 'a';
        }

        $this->validator->assertName($username);
        $this->assertTrue(true);
    }

    public function testAssertNameFiftyOneChars() {
        $this->setExpectedException(ExceptionInterface::class);
        $username = '';
        for ($i = 0; $i < 51; $i++) {
            $username .= 'a';
        }
        $this->validator->assertName($username);
    }

    public function testAssertSlugFiftyChars() {
        $username = '';
        for ($i = 0; $i < 50; $i++) {
            $username .= 'a';
        }

        $this->validator->assertSlug($username);
        $this->assertTrue(true);
    }

    public function testAssertSlugFiftyOneChars() {
        $this->setExpectedException(ExceptionInterface::class);
        $username = '';
        for ($i = 0; $i < 51; $i++) {
            $username .= 'a';
        }
        $this->validator->assertSlug($username);
    }

    public function testAssertSlugInvalidInput() {
        $this->setExpectedException(ExceptionInterface::class);
        $this->validator->assertSlug(chr(20) . chr(127));
    }

}
