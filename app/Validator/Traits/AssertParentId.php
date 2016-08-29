<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Validator\Traits;

use Respect\Validation\Validator;

/**
 * Trait to add parent id assertion.
 */
trait AssertParentId {
    /**
     * Asserts a valid parent id, digit or null.
     *
     * @param mixed $parentId
     *
     * @throws \Respect\Validation\Exceptions\ExceptionInterface
     *
     * @return void
     */
    public function assertParentId($parentId) {
        Validator::oneOf(
            Validator::nullType(),
            Validator::digit()
        )->assert($parentId);
    }
}
