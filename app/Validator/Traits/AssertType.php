<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Validator\Traits;

use Respect\Validation\Validator;

/**
 * Trait to add type assertion.
 */
trait AssertType {
    /**
     * Asserts a valid array.
     *
     * @param mixed $array
     *
     * @throws \Respect\Validation\Exceptions\ExceptionInterface
     *
     * @return void
     */
    public function assertArray($array) {
        Validator::arrayType()
            ->assert($array);
    }

    /**
     * Asserts a valid boolean.
     *
     * @param mixed $boolean
     *
     * @throws \Respect\Validation\Exceptions\ExceptionInterface
     *
     * @return void
     */
    public function assertBoolean($boolean) {
        Validator::boolType()
            ->assert($boolean);
    }

    /**
     * Asserts a valid boolean.
     *
     * @param mixed $boolean
     *
     * @throws \Respect\Validation\Exceptions\ExceptionInterface
     *
     * @return void
     */
    public function assertNullableBoolean($boolean) {
        Validator::oneOf(
            Validator::boolType(),
            Validator::nullType()
        )->assert($boolean);
    }

    /**
     * Asserts a valid float.
     *
     * @param mixed $value
     *
     * @throws \Respect\Validation\Exceptions\ExceptionInterface
     *
     * @return void
     */
    public function assertFloat($value) {
        Validator::floatType()
            ->assert($value);
    }
    /**
     * Asserts a valid string, minimum 1 char long.
     *
     * @param mixed $string
     *
     * @throws \Respect\Validation\Exceptions\ExceptionInterface
     *
     * @return void
     */
    public function assertString($string) {
        Validator::stringType()
            ->length(1, null)
            ->assert($string);
    }

    /**
     * Asserts a valid string.
     *
     * @param mixed $string
     *
     * @throws \Respect\Validation\Exceptions\ExceptionInterface
     *
     * @return void
     */
    public function assertNullableString($string) {
        Validator::oneOf(
            Validator::stringType(),
            Validator::nullType()
        )->assert($string);
    }

    /**
     * Asserts a valid short (1-50 chars long) string.
     *
     * @param mixed $string
     *
     * @throws \Respect\Validation\Exceptions\ExceptionInterface
     *
     * @return void
     */
    public function assertShortString($string) {
        Validator::stringType()
            ->length(1, 50)
            ->assert($string);
    }

    /**
     * Asserts a valid short (1-100 chars long) string.
     *
     * @param mixed $string
     *
     * @throws \Respect\Validation\Exceptions\ExceptionInterface
     *
     * @return void
     */
    public function assertMediumString($string) {
        Validator::stringType()
            ->length(1, 100)
            ->assert($string);
    }

    /**
     * Asserts a valid long (1-255 chars long) string.
     *
     * @param mixed $string
     *
     * @throws \Respect\Validation\Exceptions\ExceptionInterface
     *
     * @return void
     */
    public function assertLongString($string) {
        Validator::stringType()
            ->length(1, 255)
            ->assert($string);
    }
}