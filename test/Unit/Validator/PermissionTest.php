<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace Test\Unit\Validator;

use App\Validator\Company\Permission;
use Respect\Validation\Exceptions\ExceptionInterface;
use Test\Unit\AbstractUnit;

class PermissionTest extends AbstractUnit {
    protected $validator;

    protected function setUp() {
        $this->validator = new Permission();
    }

    public function testAssertRouteNameEmpty() {
        $this->expectedException(ExceptionInterface::class);
        $this->validator->assertRouteName('');
    }

    public function testAssertRouteNameLessThanOneChar() {
        $this->expectedException(ExceptionInterface::class);
        $this->validator->assertRouteName('');
    }

    public function testAssertRouteNameThirdyChars() {
        $this->expectedException(ExceptionInterface::class);
        $this->validator->assertRouteName('aaaaaaaaaaaaaaaaaaaaaaaaaaaaaa');
    }
}
