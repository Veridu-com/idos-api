<?php

/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types=1);

namespace App\Validator;

use Respect\Validation\Validator;

/**
 * Tag Validation Rules.
 */
class Tag implements ValidatorInterface {
    /**
     * Asserts a valid name, 1-50 chars long.
     *
     * @throws \Respect\Validation\Exceptions\ExceptionInterface
     *
     * @return void
     */
    public function assertName(string $name) {
        Validator::stringType()
            ->length(1, 50)
            ->assert($name);
    }
    /**
     * Asserts a valid slug, 1-50 chars long.
     *
     * @throws \Respect\Validation\Exceptions\ExceptionInterface
     *
     * @return void
     */
    public function assertSlug(string $slug) {
        Validator::graph()
            ->length(1, 50)
            ->assert($slug);
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
