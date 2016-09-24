<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Validator\Traits;

use Respect\Validation\Validator;

/**
 * Trait to add value assertion.
 */
trait AssertValue {
    /**
     * Asserts a valid value, minimum 1 char long.
     *
     * @param mixed $value
     *
     * @throws \Respect\Validation\Exceptions\ExceptionInterface
     *
     * @return void
     */
    public function assertValue($value) {
        Validator::prnt()
            ->length(1, null)
            ->assert($value);
    }
    /**
     * Asserts a valid value, nullable.
     *
     * @param mixed $value
     *
     * @throws \Respect\Validation\Exceptions\ExceptionInterface
     *
     * @return void
     */
    public function assertNullableValue($value) {
        Validator::oneOf(
            Validator::prnt()->length(1, null),
            Validator::nullType()
        )->assert($value);
    }
}
