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
 * Invitation Validation Rules.
 */
class Invitation implements ValidatorInterface {
    use Traits\AssertId,
        Traits\AssertEmail,
        Traits\AssertDate,
        Traits\AssertName,
        Traits\AssertString,
        Traits\AssertEntity;
}
