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
 * Setting Validation Rules.
 */
class Setting implements ValidatorInterface {
    use Traits\AssertId,
        Traits\AssertFlag,
        Traits\AssertType,
        Traits\AssertEntity,
        Traits\AssertName;
}
