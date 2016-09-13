<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Validator;

/**
 * Token Validation Rules.
 */
class Token implements ValidatorInterface {
    use Traits\AssertEntity;
}
