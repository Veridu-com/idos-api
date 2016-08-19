<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types=1);

namespace App\Validator;

use Respect\Validation\Validator;

/**
 * ServiceHandler Validation Rules.
 */
class ServiceHandler implements ValidatorInterface {
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
        Validator::stringType()
            ->assert($value);
    }

    /**
     * Asserts a valid source.
     *
     * @FIXME: what is a source? How can I validate it?
     *
     * @param mixed $value
     *
     * @throws \Respect\Validation\Exceptions\ExceptionInterface
     *
     * @return void
     */
    public function assertSource($value) {
        Validator::stringType()
            ->assert($value);
    }

    /**
     * Asserts a valid slug.
     *
     * @param mixed $value
     *
     * @throws \Respect\Validation\Exceptions\ExceptionInterface
     *
     * @return void
     */
    public function assertSlug($value) {
        Validator::graph()
            ->assert($value);
    }

    /**
     * Asserts a valid location.
     *
     * @param mixed $value
     *
     * @throws \Respect\Validation\Exceptions\ExceptionInterface
     *
     * @return void
     */
    public function assertLocation($value) {
        Validator::url()
            ->assert($value);
    }

    /**
     * Asserts a valid auth username.
     *
     * @param mixed $value
     *
     * @throws \Respect\Validation\Exceptions\ExceptionInterface
     *
     * @return void
     */
    public function assertAuthUsername($value) {
        Validator::stringType()
            ->assert($value);
    }

    /**
     * Asserts a valid auth password.
     *
     * @param mixed $value
     *
     * @throws \Respect\Validation\Exceptions\ExceptionInterface
     *
     * @return void
     */
    public function assertAuthPassword($value) {
        Validator::stringType()
            ->assert($value);
    }

    /**
     * Asserts a valid id.
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
     * Asserts a valid listens attribute.
     *
     * @param mixed $listens
     *
     * @throws \Respect\Validation\Exceptions\ExceptionInterface
     *
     * @return void
     */
    public function assertListens($value) {
        Validator::arrayType()
            ->assert($value);
    }

}
