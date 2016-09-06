<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Validator;

/**
 * Source Validation Rules.
 */
class Source implements ValidatorInterface {
    use Traits\AssertId,
        Traits\AssertIpAddr,
        Traits\AssertArray,
        Traits\ValidateFlag,
        Traits\AssertOTPCode,
        Traits\AssertSource,
        Traits\AssertUser,
        Traits\AssertName;
}
