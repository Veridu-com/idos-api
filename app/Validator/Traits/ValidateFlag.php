<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Validator\Traits;

use Respect\Validation\Validator;

/**
 * Trait to validate a flag value.
 */
trait ValidateFlag {
    /**
     * Validates a flag value.
     *
     * @param mixed  $value
     * @param string $name
     *
     * @return bool
     */
    public function validateFlag($value, string $name = 'flag') : bool {
        return Validator::trueVal()
            ->setName($name)
            ->validate($value);
    }
}
