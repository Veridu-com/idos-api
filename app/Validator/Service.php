<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Validator;

/**
 * Service Validation Rules.
 */
class Service implements ValidatorInterface {
    use Traits\AssertAccessMode,
        Traits\AssertArray,
        Traits\AssertEntity,
        Traits\AssertFlag,
        Traits\AssertId,
        Traits\AssertName,
        Traits\AssertPassword,
        Traits\AssertUrl,
        Traits\AssertUserName;
}
