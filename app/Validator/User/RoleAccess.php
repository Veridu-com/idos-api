<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Validator\User;

use App\Entity\Role;
use App\Entity\User\RoleAccess as RoleAccessEntity;
use App\Validator\Traits;
use App\Validator\ValidatorInterface;
use Respect\Validation\Validator;

/**
 * RoleAccess Validation Rules.
 */
class RoleAccess implements ValidatorInterface {
    use Traits\AssertId,
        Traits\AssertName;
    /**
     * Asserts a valid role name.
     *
     * @param string $value
     * @param string $name
     *
     * @throws \Respect\Validation\Exceptions\ExceptionInterface
     *
     * @return void
     */
    public function assertRoleName(string $value, string $name = 'roleName') : void {
        Validator::in(
            [
            Role::COMPANY,
            Role::COMPANY_OWNER,
            Role::COMPANY_ADMIN,
            Role::USER,
            Role::GUEST
            ]
        )
            ->setName($name)
            ->assert($value);
    }

    /**
     * Asserts a valid access value.
     *
     * @param int    $value
     * @param string $name
     *
     * @throws \Respect\Validation\Exceptions\ExceptionInterface
     *
     * @return void
     */
    public function assertAccess(int $value, string $name = 'access') : void {
        Validator::digit()->length(1, 1)->in(
            [
                RoleAccessEntity::ACCESS_NONE,
                RoleAccessEntity::ACCESS_EXECUTE,
                RoleAccessEntity::ACCESS_WRITE,
                RoleAccessEntity::ACCESS_READ,
                RoleAccessEntity::ACCESS_WRITE | RoleAccessEntity::ACCESS_EXECUTE,
                RoleAccessEntity::ACCESS_READ | RoleAccessEntity::ACCESS_EXECUTE,
                RoleAccessEntity::ACCESS_READ | RoleAccessEntity::ACCESS_WRITE,
                RoleAccessEntity::ACCESS_READ | RoleAccessEntity::ACCESS_WRITE | RoleAccessEntity::ACCESS_EXECUTE
            ]
        )
            ->setName($name)
            ->assert($value);
    }

    /**
     * Asserts a valid resource value.
     *
     * @param mixed  $value
     * @param string $name
     *
     * @throws \Respect\Validation\Exceptions\ExceptionInterface
     *
     * @return void
     */
    public function assertResource($value, string $name = 'resource') : void {
        Validator::stringType()
            ->setName($name)
            ->assert($value);
    }
}
