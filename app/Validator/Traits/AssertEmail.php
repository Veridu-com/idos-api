<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
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
     * @param mixed $email
     *
     * @throws \Respect\Validation\Exceptions\ExceptionInterface
     *
     * @return void
     */
    public function assertEmail($email) {
        Validator::email()
            ->assert($email);
    }
}
