<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Validator;

/**
 * ServiceHandler Validation Rules.
 */
class ServiceHandler implements ValidatorInterface {
    use Traits\AssertType,
        Traits\AssertId,
        Traits\AssertName,
        Traits\AssertPassword,
        Traits\AssertSlug,
        Traits\AssertUrl,
        Traits\AssertUserName,
        Traits\AssertEntity;
}
