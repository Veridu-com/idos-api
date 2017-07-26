<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Validator\Profile;

use App\Validator\Traits;
use App\Validator\ValidatorInterface;

/**
 * Source Validation Rules.
 */
class Source implements ValidatorInterface {
    use Traits\AssertType,
        Traits\AssertEntity,
        Traits\AssertId,
        Traits\AssertEmail,
        Traits\AssertPhone,
        Traits\AssertIpAddr,
        Traits\AssertName,
        Traits\AssertOTPCode,
        Traits\ValidateFlag;
}
