<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Validator\Traits;

use Respect\Validation\Validator;

/**
 * Trait to add source assertion.
 */
trait AssertSource {
    /**
     * Asserts a valid source entity.
     *
     * @param mixed $source
     *
     * @throws \Respect\Validation\Exceptions\ExceptionInterface
     *
     * @return void
     */
    public function assertSource($source) {
        Validator::instance('App\\Entity\\Source')
            ->assert($source);
    }
}
