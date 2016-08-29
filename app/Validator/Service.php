<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Validator;

use App\Entity\Company;
use Respect\Validation\Validator;

/**
 * Service Validation Rules.
 */
class Service implements ValidatorInterface {
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
     * Asserts a valid Company instance.
     *
     * @param App\Entity\Company $company
     *
     * @throws \Respect\Validation\Exceptions\ExceptionInterface
     *
     * @return void
     */
    public function assertCompany(Company $company) {
        Validator::objectType()
            ->assert($company);
    }

    /**
     * Asserts a valid access.
     *
     * @param mixed $value
     *
     * @throws \Respect\Validation\Exceptions\ExceptionInterface
     *
     * @return void
     */
    public function assertAccess($value) {
        Validator::intType()->in(
            [
            0x00,
            0x01,
            0x02
            ]
        )->assert($value);
    }

    /**
     * Asserts a valid enabled property.
     *
     * @param mixed $value
     *
     * @throws \Respect\Validation\Exceptions\ExceptionInterface
     *
     * @return void
     */
    public function assertEnabled($value) {
        Validator::boolType()
            ->assert($value);
    }

    /**
     * Asserts a valid url.
     *
     * @param mixed $value
     *
     * @throws \Respect\Validation\Exceptions\ExceptionInterface
     *
     * @return void
     */
    public function assertUrl($value) {
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

    /**
     * Asserts a valid triggers attribute.
     *
     * @param mixed $triggers
     *
     * @throws \Respect\Validation\Exceptions\ExceptionInterface
     *
     * @return void
     */
    public function assertTriggers($value) {
        Validator::arrayType()
            ->assert($value);
    }
}
