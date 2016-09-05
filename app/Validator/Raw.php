<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Validator;

/**
 * Raw Validation Rules.
 */
class Raw implements ValidatorInterface {
    use Traits\AssertId,
        Traits\AssertSource,
        Traits\AssertBelongsToUser,
        Traits\AssertName;
}
