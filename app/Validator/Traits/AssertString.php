<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Validator\Traits;

use Respect\Validation\Validator;

/**
 * Trait to add string assertion.
 */
trait AssertString {
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
        Validator::prnt()
            ->length(1, null)
            ->assert($string);
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
        Validator::prnt()
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
        Validator::prnt()
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
        Validator::prnt()
            ->length(1, 255)
            ->assert($string);
    }
}
