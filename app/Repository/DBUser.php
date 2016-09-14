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
use App\Exception\NotFound;
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
    public function findByCompanyId(int $companyId) : Collection {
        $result = $this->query()
            ->selectRaw('users.*')
            ->join('credentials', 'users.credential_id', '=', 'credentials.id')
            ->where('credentials.company_id', '=', $companyId);

        return $result->get();
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
            throw new NotFound();
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
            throw new NotFound('User not found.');
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
            throw new NotFound('No users related to given company found');
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getUserNameByProfileIdAndProviderNameAndCredentialId(
        string $profileId,
        string $providerName,
        int $credentialId
    ) : string {
        $result = $this->query()
            ->join('sources', 'sources.user_id', '=', 'users.id')
            ->where('sources.tags->profile_id', '=', md5($profileId))
            ->where('sources.name', '=', $providerName)
            ->where('users.credential_id', '=', $credentialId)
            ->get(['users.username']);

        return $result->first() ? $result->first()->username : '';
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
            throw new NotFound('No users related to given identity');
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
            throw new NotFound('User not found', 404);
        }

        return $result->first();
    }
}
