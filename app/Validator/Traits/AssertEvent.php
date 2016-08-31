<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Validator\Traits;

use Respect\Validation\Validator;

/**
 * Trait to add event assertion.
 */
trait AssertEvent {
    /**
     * Asserts a valid event, minimum 1 char long.
     *
     * @param mixed $event
     *
     * @throws \Respect\Validation\Exceptions\ExceptionInterface
     *
     * @return void
     */
    public function assertEvent($event) {
        Validator::prnt()
            ->length(1, null)
            ->assert($event);
    }
}
