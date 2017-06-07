<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Validator\Traits;

use Respect\Validation\Validator;

/**
 * Trait to add id assertion.
 */
trait AssertId {
    /**
     * Asserts a valid id, digit.
     *
     * @param mixed  $value
     * @param string $name
     *
     * @throws \Respect\Validation\Exceptions\ExceptionInterface
     *
     * @return void
     */
    public function assertId($value, string $name = 'id') : void {
        Validator::digit()
            ->setName($name)
            ->assert($value);
    }

    /**
     * Asserts a valid id or null.
     *
     * @param mixed  $value
     * @param string $name
     *
     * @throws \Respect\Validation\Exceptions\ExceptionInterface
     *
     * @return void
     */
    public function assertNullableId($value, string $name = 'id') : void {
        Validator::oneOf(
            Validator::digit(),
            Validator::nullType()
        )
            ->setName($name)
            ->assert($value);
    }
}
