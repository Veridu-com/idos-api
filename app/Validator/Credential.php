<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Validator;

/**
 * Credential Validation Rules.
 */
class Credential implements ValidatorInterface {
    use Traits\AssertCredential,
        Traits\AssertId,
        Traits\AssertName,
        Traits\AssertSlug,
        Traits\ValidateFlag,
        Traits\AssertFlag;
}
