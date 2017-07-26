<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Validator\Traits;

use Respect\Validation\Validator;

/**
 * Trait to add date assertion.
 */
trait AssertDate {
    /**
     * Asserts a valid date.
     *
     * @param mixed  $value
     * @param string $name
     *
     * @throws \Respect\Validation\Exceptions\ExceptionInterface
     *
     * @return void
     */
    public function assertDate($value, string $name = 'date') : void {
        Validator::date()
            ->setName($name)
            ->assert($value);
    }

    /**
     * Asserts a valid nullable date.
     *
     * @param mixed  $value
     * @param string $name
     *
     * @throws \Respect\Validation\Exceptions\ExceptionInterface
     *
     * @return void
     */
    public function assertNullableDate($value, string $name = 'date') : void {
        Validator::oneOf(
            Validator::date(),
            Validator::nullType()
        )
            ->setName($name)
            ->assert($value);
    }
}
