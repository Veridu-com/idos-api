<?php
/**
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace App\Repository;

/**
 * User Repository Interface.
 */
interface UserInterface extends RepositoryInterface {
    /**
     * Find a user by username.
     *
     * @param string $userName
     * @param int    $credentialId
     *
     * @throws App\Exception\NotFound
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function findByUserName($userName, $credentialId);
}
