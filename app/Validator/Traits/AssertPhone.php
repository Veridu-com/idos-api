<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Validator\Traits;

use Respect\Validation\Validator;

/**
 * Trait to add phone assertion.
 */
trait AssertPhone {
    /**
     * Asserts a valid phone.
     *
     * @param mixed  $value
     * @param string $name
     *
     * @throws \Respect\Validation\Exceptions\ExceptionInterface
     *
     * @return void
     */
    public function assertPhone($value, string $name = 'phone') : void {
        Validator::phone()
            ->setName($name)
            ->assert($value);
    }
}
