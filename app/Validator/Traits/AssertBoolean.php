<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Validator\Traits;

use Respect\Validation\Validator;

/**
 * Trait to add event assertion.
 */
trait AssertBoolean {
    /**
     * Asserts a valid boolean.
     *
     * @param mixed $boolean
     *
     * @throws \Respect\Validation\Exceptions\ExceptionInterface
     *
     * @return void
     */
    public function assertBoolean($boolean) {
        Validator::boolType()
            ->assert($boolean);
    }

    /**
     * Asserts a valid boolean.
     *
     * @param mixed $boolean
     *
     * @throws \Respect\Validation\Exceptions\ExceptionInterface
     *
     * @return void
     */
    public function assertNullableBoolean($boolean) {
        Validator::oneOf(
            Validator::boolType(),
            Validator::nullType()
        )->assert($boolean);
    }
}
