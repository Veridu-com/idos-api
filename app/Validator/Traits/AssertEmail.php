<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Validator\Traits;

use Respect\Validation\Validator;

/**
 * Trait to add email assertion.
 */
trait AssertEmail {
    /**
     * Asserts a valid email.
     *
     * @param mixed  $value
     * @param string $name
     *
     * @throws \Respect\Validation\Exceptions\ExceptionInterface
     *
     * @return void
     */
    public function assertEmail($value, string $name = 'email') : void {
        Validator::email()
            ->setName($name)
            ->assert($value);
    }
}
