<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Validator\Profile;

use App\Validator\Traits;
use App\Validator\ValidatorInterface;

/**
 * Process Validation Rules.
 */
class Process implements ValidatorInterface {
    use Traits\AssertId,
        Traits\AssertName,
        Traits\AssertEntity;
}
