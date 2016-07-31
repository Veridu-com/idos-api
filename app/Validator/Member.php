<?php

/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types=1);

namespace App\Validator;

use Respect\Validation\Validator;

/**
 * Member Validation Rules.
 */
class Member implements ValidatorInterface {
    /**
     * Asserts a valid name, 1-50 chars long.
     *
     * @throws \Respect\Validation\Exceptions\ExceptionInterface
     *
     * @return void
     */
    public function assertUsername(string $name) {
        Validator::graph()
            ->length(1, 50)
            ->assert($name);
    }

    /**
     * Asserts a valid company id, integer.
     *
     * @throws \Respect\Validation\Exceptions\ExceptionInterface
     *
     * @return void
     */
    public function assertCompanyId(int $id) {
        Validator::digit()
            ->assert($id);
    }

    /**
     * Asserts a valid id, integer.
     *
     * @throws \Respect\Validation\Exceptions\ExceptionInterface
     *
     * @return void
     */
    public function assertId(int $id) {
        Validator::digit()
            ->assert($id);
    }
}
