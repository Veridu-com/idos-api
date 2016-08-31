<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Validator;

/**
 * Digested Validation Rules.
 */
class Digested implements ValidatorInterface {
    use Traits\AssertId,
        Traits\AssertName,
        Traits\AssertUser,
        Traits\AssertValue;
}
