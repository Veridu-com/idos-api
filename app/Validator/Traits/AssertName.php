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
    public function assertName($name) : void {
        Validator::prnt()
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
    public function assertShortName($name) : void {
        Validator::prnt()
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
    public function assertMediumName($name) : void {
        Validator::prnt()
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
    public function assertLongName($name) : void {
        Validator::prnt()
            ->length(1, 255)
            ->assert($name);
    }
}
