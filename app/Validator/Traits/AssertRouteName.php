<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Validator\Traits;

use Respect\Validation\Validator;

/**
 * Trait to add route name assertion.
 */
trait AssertRouteName {
    /**
     * Asserts a valid name.
     *
     * @param mixed  $value
     * @param string $name
     *
     * @throws \Respect\Validation\Exceptions\ExceptionInterface
     *
     * @return void
     */
    public function assertRouteName($value, string $name = 'routeName') : void {
        Validator::regex('/[a-zA-Z]+\:[a-zA-Z]+/')
            ->length(1, 25)
            ->setName($name)
            ->assert($value);
    }
}
