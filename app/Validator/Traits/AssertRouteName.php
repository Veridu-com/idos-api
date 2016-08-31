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
     * @param mixed $value
     *
     * @throws \Respect\Validation\Exceptions\ExceptionInterface
     *
     * @return void
     */
    public function assertRouteName($value) {
        Validator::graph()
            ->length(1, 25)
            ->assert($value);
    }
}
