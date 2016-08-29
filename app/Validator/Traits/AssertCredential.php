<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Validator\Traits;

use Respect\Validation\Validator;

/**
 * Trait to add credential assertion.
 */
trait AssertCredential {
    /**
     * Asserts a valid credential entity.
     *
     * @param mixed $credential
     *
     * @throws \Respect\Validation\Exceptions\ExceptionInterface
     *
     * @return void
     */
    public function assertCredential($credential) {
        Validator::instance('App\\Entity\\Credential')
            ->assert($credential);
    }
}
