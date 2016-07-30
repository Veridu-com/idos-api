<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace App\Validator;

use App\Entity\Role;
use Respect\Validation\Validator;

/**
 * RoleAccess Validation Rules.
 */
class RoleAccess implements ValidatorInterface {
    /**
     * Asserts a valid role name.
     *
     * @throws \Respect\Validation\Exceptions\ExceptionInterface
     *
     * @return void
     */
    public function assertRoleName(string $value) {
        Validator::in([
            Role::COMPANY,
            Role::COMPANY_OWNER,
            Role::COMPANY_ADMIN,
            Role::USER,
            Role::GUEST
        ])->assert($value);
        // @FIXME shouldn't this provider a better error message?
    }

    /**
     * Asserts a valid access value.
     *
     * @throws \Respect\Validation\Exceptions\ExceptionInterface
     *
     * @return void
     */
    public function assertAccess(int $value) {
        Validator::digit()->length(1, 1)->in([
            0x00,
            0x01,
            0x02,
            0x04,
            0x05,
            0x06,
            0x07
        ])->assert($value);

    }

    /**
     * Asserts a valid access value.
     *
     * @throws \Respect\Validation\Exceptions\ExceptionInterface
     *
     * @return void
     */
    public function assertResource($value) {
        Validator::stringType()->assert($value);
    }

    /**
     * Asserts a valid id, integer.
     *
     * @throws \Respect\Validation\Exceptions\ExceptionInterface
     *
     * @return void
     */
    public function assertId($id) {
        Validator::digit()
            ->assert($id);
    }

}
