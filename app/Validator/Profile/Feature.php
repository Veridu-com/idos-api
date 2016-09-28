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
 * Feature Validation Rules.
 */
class Feature implements ValidatorInterface {
    use Traits\AssertId,
        Traits\AssertEntity,
        Traits\AssertName,
        Traits\AssertFlag,
        Traits\AssertValue,
        Traits\AssertArray;

    public function assertFeatures($features) {
    	Validator::arrayType()
            ->each(
    		  Validator::key('source_id', Validator::digit(), false) // not required
        		  ->key('name', Validator::stringType()->length(0, 50))
        		  ->key('type', Validator::stringType()->length(0, 50))
        		  ->key('value')
            )->assert($features);
    }
}
