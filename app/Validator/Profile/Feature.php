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

    public function assertFeatures($features) {
        Validator::arrayType()->assert($features);
        foreach ($features as $feature) {
            Validator::key('value')->assert($feature);
            $this->assertLongName($feature['name']);
            $this->assertName($feature['type']);

            if (key_exists('source_id', $feature)) {
                $this->assertId($feature['source_id']);
            }
        }
    }
}
