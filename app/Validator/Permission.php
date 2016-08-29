<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Validator;

use Respect\Validation\Validator;

/**
 * Permission Validation Rules.
 */
class Permission implements ValidatorInterface {
    /**
     * Asserts a valid name.
     *
     * @param mixed $value
     *
     * @throws \Respect\Validation\Exceptions\ExceptionInterface
     *
     * @return void
     */
    public function assertRouteName($value) {
        Validator::length(1, 25)
            ->assert($value);
    }

    /**
     * Asserts a valid name.
     *
     * @param mixed $value
     *
     * @throws \Respect\Validation\Exceptions\ExceptionInterface
     *
     * @return void
     */
    public function assertSectionName($value) {
        Validator::graph()
            ->assert($value);
    }

    /**
     * Asserts a valid id, integer.
     *
     * @param mixed $id
     *
     * @throws \Respect\Validation\Exceptions\ExceptionInterface
     *
     * @return void
     */
    public function assertId($id) {
        Validator::digit()
            ->assert($id);
    }
}
