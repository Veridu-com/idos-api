<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Validator\Traits;

use Respect\Validation\Validator;

/**
 * Trait to add password assertion.
 */
trait AssertPassword {
    /**
     * Asserts a valid password, minimum 6 chars long, that are graphically represented.
     *
     * @param mixed  $value
     * @param string $name
     *
     * @throws \Respect\Validation\Exceptions\ExceptionInterface
     *
     * @return void
     */
    public function assertPassword($value, string $name = 'password') : void {
        Validator::graph()
            ->length(6, null)
            ->setName($name)
            ->assert($value);
    }
}
