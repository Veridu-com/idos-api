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
        Traits\AssertType;

    public function assertFeatures($features) : void {
        Validator::arrayType()->assert($features);
        foreach ($features as $feature) {
            Validator::key('name')->assert($feature);
            Validator::key('value')->assert($feature);
            Validator::key('type')->assert($feature);
            Validator::key('source_id')->assert($feature);

            $this->assertLongName($feature['name']);
            $this->assertName($feature['type']);
            $this->assertId($feature['source_id']);
        }
    }
}
