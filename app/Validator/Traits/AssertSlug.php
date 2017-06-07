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
     * @param mixed  $value
     * @param string $name
     *
     * @throws \Respect\Validation\Exceptions\ExceptionInterface
     *
     * @return void
     */
    public function assertSlug($value, string $name = 'slug') : void {
        Validator::slug()
            ->length(1, 60)
            ->setName($name)
            ->assert($value);
    }
}
