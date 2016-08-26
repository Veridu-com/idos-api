<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Validator;

use Respect\Validation\Validator;

/**
 * Feature Validation Rules.
 */
class Feature implements ValidatorInterface {
    /**
     * Asserts a valid name.
     *
     * @param mixed $value
     *
     * @throws \Respect\Validation\Exceptions\ExceptionInterface
     *
     * @return void
     */
    public function assertName($value) {
        Validator::graph()
            ->assert($value);
    }

    /**
     * Asserts a valid id, integer.
     *
     * @param mixed $id
     *
     * @throws \Respect\Validation\Exceptions\ExceptionInterface
     *
     * @return void
     */
    public function assertId($id) {
        Validator::digit()
            ->assert($id);
    }

    /**
     * Asserts a valid slug, 1-15 chars long.
     *
     * @param mixed $slug
     *
     * @throws \Respect\Validation\Exceptions\ExceptionInterface
     *
     * @return void
     */
    public function assertSlug($slug) {
        Validator::slug()
            ->length(1, 15)
            ->assert($slug);
    }
}
