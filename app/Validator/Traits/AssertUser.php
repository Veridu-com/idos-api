<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Validator\Traits;

use Respect\Validation\Validator;

/**
 * Trait to add user assertion.
 */
trait AssertUser {
    /**
     * Asserts a valid user entity.
     *
     * @param mixed $user
     *
     * @throws \Respect\Validation\Exceptions\ExceptionInterface
     *
     * @return void
     */
    public function assertUser($user) {
        Validator::instance('App\\Entity\\User')
            ->assert($user);
    }
}
