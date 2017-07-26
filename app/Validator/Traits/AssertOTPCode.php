<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Validator\Traits;

use Respect\Validation\Validator;

/**
 * Trait to add OTP Code assertion.
 */
trait AssertOTPCode {
    /**
     * Asserts a valid otp code, 6 digits long.
     *
     * @param mixed  $value
     * @param string $name
     *
     * @throws \Respect\Validation\Exceptions\ExceptionInterface
     *
     * @return void
     */
    public function assertOTPCode($value, string $name = 'code') : void {
        Validator::digit()
            ->length(6, null)
            ->setName($name)
            ->assert($value);
    }
}
