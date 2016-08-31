<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository;

use App\Entity\Company;
use App\Entity\User;
use Illuminate\Support\Collection;

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
     * @return App\Entity\User
     */
    public function findByUserName(string $userName, int $credentialId) : User;
}
