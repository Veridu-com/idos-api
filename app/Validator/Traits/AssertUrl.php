<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Validator\Traits;

use Respect\Validation\Validator;

/**
 * Trait to add url assertion.
 */
trait AssertUrl {
    /**
     * Asserts a valid url.
     *
     * @param mixed  $value
     * @param string $name
     *
     * @throws \Respect\Validation\Exceptions\ExceptionInterface
     *
     * @return void
     */
    public function assertUrl($value, string $name = 'url') : void {
        Validator::url()
            ->setName($name)
            ->assert($value);
    }
}
