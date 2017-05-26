<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Validator\Profile;

use App\Validator\Traits;
use App\Validator\ValidatorInterface;
use Respect\Validation\Validator;

/**
 * Attribute Validation Rules.
 */
class Attribute implements ValidatorInterface {
    use Traits\AssertEntity,
        Traits\AssertName,
        Traits\AssertValue,
        Traits\AssertType;

    public function assertAttributeArray($attributes) : void {
        Validator::arrayType()->assert($attributes);

        foreach ($attributes as $attribute) {
            Validator::key('user_id')->assert($attribute);
            Validator::key('name')->assert($attribute);
            Validator::key('value')->assert($attribute);
        }
    }
}
