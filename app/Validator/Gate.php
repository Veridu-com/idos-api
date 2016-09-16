<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Validator;

/**
 * Gate Validation Rules.
 */
class Gate implements ValidatorInterface {
    use Traits\AssertId,
        Traits\AssertEntity,
        Traits\AssertName,
        Traits\AssertSlug,
        Traits\AssertFlag,
        Traits\ValidateFlag;
}
