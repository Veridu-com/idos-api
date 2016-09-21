<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace Test\Unit\Validator;

use App\Validator\Company\Hook;
use Respect\Validation\Exceptions\ExceptionInterface;
use Test\Unit\AbstractUnit;

class HookTest extends AbstractUnit {
    protected $validator;

    protected function setUp() {
        $this->validator = new Hook();
    }

    public function testAssertId() {
        $this->validator->assertId(1);
        $this->assertTrue(true);
    }

    public function testAssertTriggerFiftyChars() {
        $trigger = '';
        for ($i = 0; $i < 50; $i++) {
            $trigger .= 'a';
        }

        $this->validator->assertTriggerName($trigger);
        $this->assertTrue(true);
    }

    public function testAssertTriggerFiftyOneChars() {
        $this->setExpectedException(ExceptionInterface::class);
        $trigger = '';
        for ($i = 0; $i < 51; $i++) {
            $trigger .= 'a';
        }

        $this->validator->assertTriggerName($trigger);
    }

    public function testAssertTriggerInvalidInput() {
        $this->setExpectedException(ExceptionInterface::class);
        $this->validator->assertTriggerName(chr(20) . chr(127));
    }

    public function testAssertUrl() {
        $url = 'http://example.com/test.php';

        $this->validator->assertUrl($url);
        $this->assertTrue(true);
    }
}
