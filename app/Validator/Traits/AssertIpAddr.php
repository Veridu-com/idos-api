<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
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
     * @param mixed  $value
     * @param string $name
     *
     * @throws \Respect\Validation\Exceptions\ExceptionInterface
     *
     * @return void
     */
    public function assertIpAddr($value, string $name = 'ipaddr') : void {
        Validator::ip()
            ->setName($name)
            ->assert($value);
    }
}
