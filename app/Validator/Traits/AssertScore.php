<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Validator\Traits;

use Respect\Validation\Validator;

/**
 * Trait to add score assertion.
 */
trait AssertScore {
    /**
     * Asserts a valid score, float between 0 and 1.
     *
     * @param mixed  $value
     * @param string $name
     *
     * @throws \Respect\Validation\Exceptions\ExceptionInterface
     *
     * @return void
     */
    public function assertScore($value, string $name = 'score') : void {
        Validator::oneOf(
            Validator::intType(),
            Validator::floatType()
        )
            ->between(0, 1, true)
            ->setName($name)
            ->assert($value);
    }
}
