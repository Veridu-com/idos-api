<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Validator;

/**
 * Metric Validation Rules.
 */
class Metric implements ValidatorInterface {
    use Traits\AssertType;
    use Traits\AssertEntity;
}
