<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
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
     * Validates if $input is compound of latin chars.
     *
     * @param mixed $input
     *
     * @return bool
     */
    public function latinChars($input) : bool {
        $alphabet = array_merge(
            [32],
            range(65, 90),
            range(97, 122),
            range(195, 207)
        );
        foreach (str_split($input) as $char) {
            if (! in_array(ord($char), $alphabet)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Asserts a valid name, minimum 1 char long.
     *
     * @param mixed $name
     *
     * @throws \Respect\Validation\Exceptions\ExceptionInterface
     *
     * @return void
     */
    public function assertName($name) {
        Validator::prnt()
            ->length(1, null)
            ->assert($name);
    }

    /**
     * Asserts a valid latin name, minimum 1 char long.
     *
     * @param mixed $name
     *
     * @return void
     */
    public function assertLatinName($name) {
        Validator::callback([$this, 'latinChars'])
            ->length(1, null)
            ->assert($name);
    }

    /**
     * Asserts a valid short (1-50 chars long) name.
     *
     * @param mixed $name
     *
     * @throws \Respect\Validation\Exceptions\ExceptionInterface
     *
     * @return void
     */
    public function assertShortName($name) {
        Validator::prnt()
            ->length(1, 50)
            ->assert($name);
    }

    /**
     * Asserts a valid short (1-50 chars long) latin name.
     *
     * @param mixed $name
     *
     * @return void
     */
    public function assertShortLatinName($name) {
        Validator::callback([$this, 'latinChars'])
            ->length(1, 50)
            ->assert($name);
    }

    /**
     * Asserts a valid short (1-100 chars long) name.
     *
     * @param mixed $name
     *
     * @throws \Respect\Validation\Exceptions\ExceptionInterface
     *
     * @return void
     */
    public function assertMediumName($name) {
        Validator::prnt()
            ->length(1, 100)
            ->assert($name);
    }

    /**
     * Asserts a valid medium (1-100 chars long) latin name.
     *
     * @param mixed $name
     *
     * @return void
     */
    public function assertMediumLatinName($name) {
        Validator::callback([$this, 'latinChars'])
            ->length(1, 100)
            ->assert($name);
    }

    /**
     * Asserts a valid long (1-255 chars long) name.
     *
     * @param mixed $name
     *
     * @throws \Respect\Validation\Exceptions\ExceptionInterface
     *
     * @return void
     */
    public function assertLongName($name) {
        Validator::prnt()
            ->length(1, 255)
            ->assert($name);
    }

    /**
     * Asserts a valid long (1-150 chars long) latin name.
     *
     * @param mixed $name
     *
     * @return void
     */
    public function assertLongLatinName($name) {
        Validator::callback([$this, 'latinChars'])
            ->length(1, 150)
            ->assert($name);
    }
}
