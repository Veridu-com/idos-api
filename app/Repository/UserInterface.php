<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository;

use App\Entity\User;

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

    /**
     * Generates a signed JWT.
     *
     * @param string $username          The username
     * @param string $credentialPrivKey The credential priv key
     * @param string $credentialPubKey  The credential pub key
     */
    public static function generateToken($username, string $credentialPrivKey, string $credentialPubKey) : string;
}
