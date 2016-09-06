<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Validator;

/**
 * Review Validation Rules.
 */
class Review implements ValidatorInterface {
    use Traits\AssertId,
        Traits\AssertUser,
        Traits\AssertFlag,
        Traits\AssertName,
        Traits\ValidateFlag;
}
