<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Credential;
use App\Entity\User;
use App\Exception\NotFound;
use Lcobucci\JWT;

/**
 * Database-based User Repository Implementation.
 */
class DBUser extends AbstractDBRepository implements UserInterface {
    /**
     * The table associated with the repository.
     *
     * @var string
     */
    protected $tableName = 'users';
    /**
     * The entity associated with the repository.
     *
     * @var string
     */
    protected $entityName = 'User';

    /**
     * {@inheritdoc}
     */
    public function findByUserNameAndCompany(string $userName, int $companyId) : User {
        $result = $this->query()
            ->join('credentials', 'credential_id', '=', 'credentials.id')
            ->where('credentials.company_id', '=', $companyId)
            ->first(['users.*']);

        if (empty($result)) {
            throw new NotFound();
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function findByUserName(string $username, int $credentialId) : User {
        return $this->findOneBy([
            'username'      => $username,
            'credential_id' => $credentialId
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function findByPubKey(string $publicKey) {
        $result = $this->query()
            ->selectRaw('users.*')
            ->join('credentials', 'users.credential_id', '=', 'credentials.id')
            ->where('credentials.public', '=', $publicKey)
            ->first();

        if (empty($result)) {
            throw new NotFound();
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function findByPrivKey(string $privateKey) {
        $result = $this->query()
            ->selectRaw('users.*')
            ->join('credentials', 'users.credential_id', '=', 'credentials.id')
            ->where('credentials.private', '=', $privateKey)
            ->first();

        if (empty($result)) {
            throw new NotFound();
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function findOrCreate(string $userName, int $credentialId) : User {
        $result = $this->query()
            ->where('username', $userName)
            ->where('credential_id', $credentialId)
            ->first();
        if (empty($result)) {
            $user = $this
                ->create([
                    'username'      => $userName,
                    'credential_id' => $credentialId
                ]);

            $result = $this->save($user);
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function findOneByUsernameAndCredential(string $userName, Credential $credential) : User {
        $result = $this->query()
            ->where('username', $userName)
            ->where('credential_id', $credential->id)
            ->first();

        if (empty($result)) {
            throw new NotFound('User not found.');
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function findOneByUsernameAndCredentialId(string $userName, int $credentialId) : User {
        $result = $this->query()
            ->where('username', $userName)
            ->where('credential_id', $credentialId)
            ->first();

        if (empty($result)) {
            throw new NotFound('User not found.');
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function generateToken(string $username, string $credentialPrivKey, string $credentialPubKey) : string {
        $jwtParser     = new JWT\Parser();
        $jwtValidation = new JWT\ValidationData();
        $jwtSigner     = new JWT\Signer\Hmac\Sha256();
        $jwtBuilder    = new JWT\Builder();

        $jwtBuilder->set('iss', $credentialPubKey);
        $jwtBuilder->set('sub', $username);

        return $jwtBuilder
                ->sign($jwtSigner, $credentialPrivKey)
                ->getToken();
    }

}
