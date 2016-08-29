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
     * Asserts a valid short (1-15 chars long) name.
     *
     * @param mixed $name
     *
     * @throws \Respect\Validation\Exceptions\ExceptionInterface
     *
     * @return void
     */
    public function assertShortName($name) {
        Validator::prnt()
            ->length(1, 15)
            ->assert($name);
    }

    /**
     * Asserts a valid short (1-30 chars long) name.
     *
     * @param mixed $name
     *
     * @throws \Respect\Validation\Exceptions\ExceptionInterface
     *
     * @return void
     */
    public function assertMediumName($name) {
        Validator::prnt()
            ->length(1, 30)
            ->assert($name);
    }

    /**
     * Asserts a valid long (1-60 chars long) name.
     *
     * @param mixed $name
     *
     * @throws \Respect\Validation\Exceptions\ExceptionInterface
     *
     * @return void
     */
    public function assertLongName($name) {
        Validator::prnt()
            ->length(1, 60)
            ->assert($name);
    }
}
