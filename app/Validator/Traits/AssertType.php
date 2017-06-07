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
     * @param mixed  $value
     * @param string $name
     *
     * @throws \Respect\Validation\Exceptions\ExceptionInterface
     *
     * @return void
     */
    public function assertArray($value, string $name = 'array') : void {
        Validator::arrayType()
            ->setName($name)
            ->assert($value);
    }

    /**
     * Asserts a valid array.
     *
     * @param mixed  $value
     * @param string $name
     *
     * @throws \Respect\Validation\Exceptions\ExceptionInterface
     *
     * @return void
     */
    public function assertNullableArray($value, string $name = 'array') : void {
        Validator::oneOf(
            Validator::arrayType(),
            Validator::nullType()
        )
            ->setName($name)
            ->assert($value);
    }

    /**
     * Asserts a valid boolean.
     *
     * @param mixed  $value
     * @param string $name
     *
     * @throws \Respect\Validation\Exceptions\ExceptionInterface
     *
     * @return void
     */
    public function assertBoolean($value, string $name = 'boolean') : void {
        Validator::boolType()
            ->setName($name)
            ->assert($value);
    }

    /**
     * Asserts a valid boolean.
     *
     * @param mixed  $value
     * @param string $name
     *
     * @throws \Respect\Validation\Exceptions\ExceptionInterface
     *
     * @return void
     */
    public function assertNullableBoolean($value, string $name = 'boolean') : void {
        Validator::oneOf(
            Validator::boolType(),
            Validator::nullType()
        )
            ->setName($name)
            ->assert($value);
    }

    /**
     * Asserts a valid float.
     *
     * @param mixed  $value
     * @param string $name
     *
     * @throws \Respect\Validation\Exceptions\ExceptionInterface
     *
     * @return void
     */
    public function assertFloat($value, string $name = 'float') : void {
        Validator::floatType()
            ->setName($name)
            ->assert($value);
    }
    /**
     * Asserts a valid string, minimum 1 char long.
     *
     * @param mixed  $value
     * @param string $name
     *
     * @throws \Respect\Validation\Exceptions\ExceptionInterface
     *
     * @return void
     */
    public function assertString($value, string $name = 'string') : void {
        Validator::stringType()
            ->length(1, null)
            ->setName($name)
            ->assert($value);
    }

    /**
     * Asserts a valid string.
     *
     * @param mixed  $value
     * @param string $name
     *
     * @throws \Respect\Validation\Exceptions\ExceptionInterface
     *
     * @return void
     */
    public function assertNullableString($value, string $name = 'string') : void {
        Validator::oneOf(
            Validator::stringType(),
            Validator::nullType()
        )
            ->setName($name)
            ->assert($value);
    }

    /**
     * Asserts a valid short (1-50 chars long) string.
     *
     * @param mixed  $value
     * @param string $name
     *
     * @throws \Respect\Validation\Exceptions\ExceptionInterface
     *
     * @return void
     */
    public function assertShortString($value, string $name = 'string') : void {
        Validator::stringType()
            ->length(1, 50)
            ->setName($name)
            ->assert($value);
    }

    /**
     * Asserts a valid short (1-100 chars long) string.
     *
     * @param mixed  $value
     * @param string $name
     *
     * @throws \Respect\Validation\Exceptions\ExceptionInterface
     *
     * @return void
     */
    public function assertMediumString($value, string $name = 'string') : void {
        Validator::stringType()
            ->length(1, 100)
            ->setName($name)
            ->assert($value);
    }

    /**
     * Asserts a valid long (1-255 chars long) string.
     *
     * @param mixed  $value
     * @param string $name
     *
     * @throws \Respect\Validation\Exceptions\ExceptionInterface
     *
     * @return void
     */
    public function assertLongString($value, string $name = 'string') : void {
        Validator::stringType()
            ->length(1, 255)
            ->setName($name)
            ->assert($value);
    }
}
