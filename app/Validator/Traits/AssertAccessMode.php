<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Validator\Traits;

use Respect\Validation\Validator;

/**
 * Trait to add access mode assertion.
 */
trait AssertAccessMode {
    /**
     * Asserts a valid access mode.
     *
     * @param mixed $accessMode
     *
     * @throws \Respect\Validation\Exceptions\ExceptionInterface
     *
     * @return void
     */
    public function assertAccessMode($accessMode) : void {
        Validator::intType()->in(
            [
                0x00,
                0x01,
                0x02
            ]
        )->assert($accessMode);
    }
}
