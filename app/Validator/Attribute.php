<?php

/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types=1);

namespace App\Validator;

use Respect\Validation\Validator;

/**
 * Attribute Validation Rules.
 */
class Attribute implements ValidatorInterface {
    /**
     * Asserts a valid name, 1-50 chars long.
     *
     * @throws \Respect\Validation\Exceptions\ExceptionInterface
     *
     * @return void
     */
    public function assertName(string $name) {
        Validator::stringType()
            ->length(1, 50)
            ->assert($name);
    }
    /**
     * Asserts a valid value.
     *
     * @throws \Respect\Validation\Exceptions\ExceptionInterface
     *
     * @return void
     */
    public function assertValue(string $value) {
        Validator::stringType()
            ->assert($value);
    }
    /**
     * Asserts a valid id, integer.
     *
     * @throws \Respect\Validation\Exceptions\ExceptionInterface
     *
     * @return void
     */
    public function assertId(int $id) {
        Validator::digit()
            ->assert($id);
    }
}
