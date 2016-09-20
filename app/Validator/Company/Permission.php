<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Validator\Company;

use App\Validator\Traits;
use App\Validator\ValidatorInterface;

/**
 * Permission Validation Rules.
 */
class Permission implements ValidatorInterface {
    use Traits\AssertId,
        Traits\AssertRouteName;
}
