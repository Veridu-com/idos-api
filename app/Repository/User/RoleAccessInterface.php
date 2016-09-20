<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository\User;

use App\Entity\User\RoleAccess;
use Illuminate\Support\Collection;
use App\Repository\RepositoryInterface;

/**
 * RoleAccess Repository Interface.
 */
interface RoleAccessInterface extends RepositoryInterface {
    /**
     * Find a role access by the user's identity id and the role route.
     *
     * @param int    $identityId
     * @param string $role
     *
     * @throws App\Exception\NotFound
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function findByIdentityAndRole(int $identityId, string $role) :  Collection;

    /**
     * Find role accesses by the user's identity id.
     *
     * @param int $identityId
     *
     * @throws App\Exception\NotFound
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function findByIdentity(int $identityId) : Collection;

    /**
     * Find a role access by the user's identity id.
     *
     * @param int $roleAccessId
     * @param int $identityId
     *
     * @throws App\Exception\NotFound
     *
     * @return \Illuminate\Database\Eloquent\Model|0
     */
    public function findOne(int $identityId, int $roleAccessId) :  RoleAccess;

    /**
     * Find a role access by the user's identity id.
     *
     * @param int $roleAccessId
     * @param int $identityId
     *
     * @throws App\Exception\NotFound
     *
     * @return int affected rows
     */
    public function deleteOne(int $identityId, int $roleAccessId) :  int;

    /**
     * Deletes all role access configuration of the gven identity.
     *
     * @param int $identityId
     *
     * @throws App\Exception\NotFound
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function deleteAllFromIdentity(int $identityId) : int;
}
