<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Validator\Traits;

use Respect\Validation\Validator;

/**
 * Trait to add flag assertion.
 */
trait AssertFlag {
    /**
     * Asserts a valid  flag, boolean.
     *
     * @param mixed $flag
     *
     * @throws \Respect\Validation\Exceptions\ExceptionInterface
     *
     * @return void
     */
    public function assertFlag($flag) : void {
        Validator::boolVal()
            ->assert($flag);
    }
}
