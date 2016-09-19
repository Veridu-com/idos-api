<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Validator\Traits;

use Respect\Validation\Validator;

/**
 * Trait to add string assertion.
 */
trait AssertString {
    /**
     * Asserts a valid long (1-60 chars long) string.
     *
     * @param mixed $string
     *
     * @return void
     */
    public function assertLongString($string) {
        Validator::stringType()
            ->length(1, 60)
            ->assert($string);
    }
}
