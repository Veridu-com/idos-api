<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Validator;

/**
 * Service Validation Rules.
 */
class Service implements ValidatorInterface {
    use Traits\AssertType,
        Traits\AssertId,
        Traits\AssertName,
        Traits\AssertPassword,
        Traits\AssertSlug,
        Traits\AssertUrl,
        Traits\AssertUserName,
        Traits\AssertEntity;
}
