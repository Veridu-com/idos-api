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
trait AssertIdOrNull {
    /**
     * Asserts a valid id or null.
     *
     * @param mixed $bolean
     *
     * @throws \Respect\Validation\Exceptions\ExceptionInterface
     *
     * @return void
     */
    public function assertIdOrNull($id) {
        Validator::oneOf(
            Validator::digit(),
            Validator::nullType()
        )->assert($id);
    }
}
