<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Validator\Company;

use App\Validator\Traits;
use App\Validator\ValidatorInterface;

/**
 * Widget Validation Rules.
 */
class Widget implements ValidatorInterface {
    use Traits\AssertEntity,
        Traits\AssertId,
        Traits\AssertString,
        Traits\AssertName;
}
