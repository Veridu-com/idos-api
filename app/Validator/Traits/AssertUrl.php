<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
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
     * @param mixed $url
     *
     * @throws \Respect\Validation\Exceptions\ExceptionInterface
     *
     * @return void
     */
    public function assertUrl($url) : void {
        Validator::url()
            ->assert($url);
    }
}
