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
     * @param mixed $flag
     *
     * @return bool
     */
    public function validateFlag($flag) : bool {
        return Validator::trueVal()
            ->validate($flag);
    }
}
