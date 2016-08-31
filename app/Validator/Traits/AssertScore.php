<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
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
     * @param mixed $score
     *
     * @throws \Respect\Validation\Exceptions\ExceptionInterface
     *
     * @return void
     */
    public function assertScore($score) {
        Validator::floatType()
            ->between(0, 1, true)
            ->assert($score);
    }
}
