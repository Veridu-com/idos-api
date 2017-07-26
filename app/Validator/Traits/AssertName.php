<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Validator\Traits;

use Respect\Validation\Validator;

/**
 * Trait to add name assertion.
 */
trait AssertName {
    /**
     * Asserts a valid name, minimum 1 char long.
     *
     * @param mixed  $value
     * @param string $name
     *
     * @throws \Respect\Validation\Exceptions\ExceptionInterface
     *
     * @return void
     */
    public function assertName($value, string $name = 'name') : void {
        Validator::prnt()
            ->length(1, null)
            ->setName($name)
            ->assert($value);
    }

    /**
     * Asserts a valid short (1-50 chars long) name.
     *
     * @param mixed  $value
     * @param string $name
     *
     * @throws \Respect\Validation\Exceptions\ExceptionInterface
     *
     * @return void
     */
    public function assertShortName($value, string $name = 'name') : void {
        Validator::prnt()
            ->length(1, 50)
            ->setName($name)
            ->assert($value);
    }

    /**
     * Asserts a valid short (1-100 chars long) name.
     *
     * @param mixed  $value
     * @param string $name
     *
     * @throws \Respect\Validation\Exceptions\ExceptionInterface
     *
     * @return void
     */
    public function assertMediumName($value, string $name = 'name') : void {
        Validator::prnt()
            ->length(1, 100)
            ->setName($name)
            ->assert($value);
    }

    /**
     * Asserts a valid long (1-255 chars long) name.
     *
     * @param mixed  $value
     * @param string $name
     *
     * @throws \Respect\Validation\Exceptions\ExceptionInterface
     *
     * @return void
     */
    public function assertLongName($value, string $name = 'name') : void {
        Validator::prnt()
            ->length(1, 255)
            ->setName($name)
            ->assert($value);
    }
}
