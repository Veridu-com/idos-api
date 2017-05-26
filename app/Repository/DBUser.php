<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository;

use App\Entity\Company;
use App\Entity\User;
use App\Exception\AppException;
use App\Exception\Create\IdentityException;
use App\Exception\NotFound\UserException;
use Illuminate\Support\Collection;

/**
 * Database-based User Repository Implementation.
 */
class DBUser extends AbstractSQLDBRepository implements UserInterface {
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
    protected $relationships = [
        'credential' => [
            'type'       => 'MANY_TO_ONE',
            'table'      => 'credentials',
            'foreignKey' => 'credential_id',
            'key'        => 'id',
            'entity'     => 'Credential',
            'nullable'   => false,
            'hydrate'    => [
                'public'
            ]
        ],
    ];

    /**
     * {@inheritdoc}
     */
    public function findByCompanyId(int $companyId) : Collection {
        return $this->findBy(
            [
            'credential.company_id' => $companyId
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function findByUserNameAndCompany(string $userName, int $companyId) : User {
        $result = $this->query()
            ->join('credentials', 'credential_id', '=', 'credentials.id')
            ->where('credentials.company_id', '=', $companyId)
            ->where('users.username', '=', $userName)
            ->first(['users.*']);

        if (empty($result)) {
            throw new UserException();
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function findByUserName(string $username, int $credentialId) : User {
        return $this->findOneBy(
            [
                'username'      => $username,
                'credential_id' => $credentialId
            ]
        );
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
                ->create(
                    [
                        'username'      => $userName,
                        'credential_id' => $credentialId
                    ]
                );

            $result = $this->save($user);
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
            throw new UserException('User not found.');
        }

        return $result;
    }

    public function findAllRelatedToCompany(User $user, Company $company) : Collection {
        if (! $user->identityId) {
            throw new AppException('User without identity');
        }

        $result = $this->query()
            ->join('credentials', 'credentials.id', '=', 'users.credential_id')
            ->join('roles', 'users.role', '=', 'roles.name')
            ->where('users.identity_id', '=', $user->identityId)
            ->where('users.role', 'LIKE', 'company%')
            ->where('credentials.company_id', '=', $company->id)
            ->orderBy('roles.rank', 'asc')
            ->get(['users.*', 'credentials.public']);

        if ($result->isEmpty()) {
            throw new UserException('No users related to given company found');
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function findOneByProfileIdAndProviderNameAndCredentialId(
        string $profileId,
        string $providerName,
        int $credentialId
    ) : User {
        $user = $this->query()
            ->join('sources', 'sources.user_id', '=', 'users.id')
            ->where('sources.tags->profile_id', '=', md5($profileId))
            ->where('sources.name', '=', $providerName)
            ->where('users.credential_id', '=', $credentialId)
            ->first(['users.*']);

        if (empty($user)) {
            throw new UserException();
        }

        return $user;
    }

    /**
     * {@inheritdoc}
     */
    public function findAllByIdentityId(int $identityId) : Collection {
        $result = $this->query()
            ->join('links', 'links.user_id', '=', 'users.id')
            ->where('links.identity_id', '=', $identityId)
            ->get(['users.*']);

        if ($result->isEmpty()) {
            throw new UserException('No users related to given identity');
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function findOneByIdentityIdAndCompanyId(int $identityId, int $companyId) : User {
        $result = $this->query()
            ->join('credentials', 'credentials.id', '=', 'users.credential_id')
            ->join('companies', 'companies.id', '=', 'credentials.company_id')
            ->where('users.identity_id', '=', $identityId)
            ->where('companies.id', '=', $companyId)
            ->get(['users.*']);

        if ($result->isEmpty()) {
            throw new UserException('User not found', 404);
        }

        return $result->first();
    }

    /**
     * {@inheritdoc}
     */
    public function findOneByIdentityIdAndCredentialId(int $identityId, int $credentialId) : User {
        $user = $this->query()
            ->join('user_identities', 'user_identities.user_id', 'users.id')
            ->where('user_identities.identity_id', $identityId)
            ->where('users.credential_id', $credentialId)
            ->first(['users.*']);

        if (empty($user)) {
            throw new UserException('User not found', 404);
        }

        return $user;
    }

    /**
     * {@inheritdoc}
     */
    public function assignIdentityToUser(int $userId, int $identityId) : void {
        $query    = 'INSERT INTO user_identities (identity_id, user_id) VALUES (:identityId, :userId)';
        $bindings = [
            'userId'     => $userId,
            'identityId' => $identityId
        ];

        if (! $this->runRaw($query, $bindings)) {
            throw new IdentityException();
        }
    }
}
