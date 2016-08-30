<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Validator\Traits;

use Respect\Validation\Validator;

/**
 * Trait to add hook assertion.
 */
trait AssertHook {
    /**
     * Asserts a valid hook entity.
     *
     * @param mixed $hook
     *
     * @throws \Respect\Validation\Exceptions\ExceptionInterface
     *
     * @return void
     */
    public function assertHook($hook) {
        Validator::instance('App\\Entity\\Hook')
            ->assert($hook);
    }
}
