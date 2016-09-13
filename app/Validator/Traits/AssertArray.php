<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Validator\Traits;

use Respect\Validation\Validator;

/**
 * Trait to add array assertion.
 */
trait AssertArray {
    /**
     * Asserts a valid array.
     *
     * @param mixed $array
     *
     * @throws \Respect\Validation\Exceptions\ExceptionInterface
     *
     * @return void
     */
    public function assertArray($array) {
        Validator::arrayType()
            ->assert($array);
    }
}
