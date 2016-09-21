<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Validator\Traits;

use Respect\Validation\Validator;

/**
 * Trait to add slug assertion.
 */
trait AssertSlug {
    /**
     * Asserts a valid slug, 1-15 chars long.
     *
     * @param mixed $slug
     *
     * @throws \Respect\Validation\Exceptions\ExceptionInterface
     *
     * @return void
     */
    public function assertSlug($slug) {
        Validator::slug()
            ->length(1, 60)
            ->assert($slug);
    }
}
