<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace Test\Unit\Validator;

use App\Validator\Permission;
use Respect\Validation\Exceptions\ExceptionInterface;
use Test\Unit\AbstractUnit;

class PermissionTest extends AbstractUnit {
    protected $validator;

    protected function setUp() {
        $this->validator = new Permission();
    }

    public function testAssertId() {
        $this->validator->assertId(1);
    }

    public function testAssertRouteNameEmpty() {
        $this->setExpectedException(ExceptionInterface::class);
        $this->validator->assertRouteName('');
    }

    public function testAssertRouteNameLessThanOneChar() {
        $this->setExpectedException(ExceptionInterface::class);
        $this->validator->assertRouteName('');
    }

    public function testAssertRouteNameThirdyChars() {
        $this->setExpectedException(ExceptionInterface::class);
        $this->validator->assertRouteName('aaaaaaaaaaaaaaaaaaaaaaaaaaaaaa');
    }

}
