<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Validator\Company;

use App\Validator\Traits;
use App\Validator\ValidatorInterface;

/**
 * Member Validation Rules.
 */
class Member implements ValidatorInterface {
    use Traits\AssertId,
        Traits\AssertDate,
        Traits\AssertEmail,
        Traits\AssertName,
        Traits\AssertEntity,
        Traits\AssertUserName;
}
