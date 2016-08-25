<?php

/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Validator;

use Respect\Validation\Validator;

/**
 * Hook Validation Rules.
 */
class Hook implements ValidatorInterface {
    /**
     * Asserts a valid trigger, 1-50 chars long.
     *
     * @throws \Respect\Validation\Exceptions\ExceptionInterface
     *
     * @return void
     */
    public function assertTrigger(string $trigger) {
        Validator::graph()
            ->length(1, 50)
            ->assert($trigger);
    }

    /**
     * Asserts a valid url.
     *
     * @throws \Respect\Validation\Exceptions\ExceptionInterface
     *
     * @return void
     */
    public function assertUrl(string $url) {
        Validator::graph()
            ->assert($url);
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
