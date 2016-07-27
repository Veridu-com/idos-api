<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace App\Validator;

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
    public function assertRoleName($value) {
        Validator::in([
            'company',
            'company.owner',
            'company.admin',
            'company.member',
            'user',
            'guest'
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
    public function assertAccess($value) {
        Validator::stringType()->length(1,1)->in([
            '0',
            '1',
            '2',
            '4',
            '5',
            '6',
            '7'
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
        $split = explode('.', $value);
        Validator::in(['get', 'post', 'put'])->assert($split[0]);
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
