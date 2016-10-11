<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Validator\Traits;

use Respect\Validation\Validator;

/**
 * Trait to add date assertion.
 */
trait AssertDate {
    /**
     * Asserts a valid date.
     *
     * @param mixed $date
     *
     * @throws \Respect\Validation\Exceptions\ExceptionInterface
     *
     * @return void
     */
    public function assertDate($date) {
        Validator::date()
            ->assert($date);
    }
}
