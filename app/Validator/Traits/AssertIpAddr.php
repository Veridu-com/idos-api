<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Validator\Traits;

use Respect\Validation\Validator;

/**
 * Trait to add ip address assertion.
 */
trait AssertIpAddr {
    /**
     * Asserts a valid ip address.
     *
     * @param mixed $ipAddr
     *
     * @throws \Respect\Validation\Exceptions\ExceptionInterface
     *
     * @return void
     */
    public function assertIpAddr($ipAddr) {
        Validator::Ip()
            ->assert($ipAddr);
    }
}
