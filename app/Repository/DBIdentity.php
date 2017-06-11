<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository;

use App\Entity\Identity;
use App\Exception\NotFound;
use Illuminate\Support\Collection;

/**
 * Database-based Identity Repository Implementation.
 */
class DBIdentity extends AbstractDBRepository implements IdentityInterface {
    /**
     * The table associated with the repository.
     *
     * @var string
     */
    protected $tableName = 'identities';
    /**
     * The entity associated with the repository.
     *
     * @var string
     */
    protected $entityName = 'Identity';

    private $queryColumns = [
        'identities.*',
        'companies.id as company.id',
        'companies.name as company.name',
        'companies.slug as company.slug',
        'companies.public_key as company.public_key',
        'companies.created_at as company.created_at',
        'companies.updated_at as company.updated_at',
        'members.id as member.id',
        'members.role as member.role',
        'members.company_id as member.company',
        'members.created_at as member.created_at',
        'members.updated_at as member.updated_at',
        'roles.id as role.id',
        'roles.name as role.name',
        'roles.rank as role.rank',
        'roles.bit as role.bit',
        'roles.created_at as role.created_at'
    ];

    public function getReference(
        string $sourceName,
        string $profileId,
        string $appKey
    ) : string {
        return md5(
            sprintf(
                '%s:%s:%s',
                $sourceName,
                $profileId,
                $appKey
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function findByPubKey(string $pubKey) : Identity {
        $companyRepository = $this->repositoryFactory->create('Company');

        $identities = $this->query()
            ->leftJoin('members', 'members.identity_id', 'identities.id')
            ->leftJoin('roles', 'members.role', 'roles.name')
            ->leftJoin('companies', 'companies.id', 'members.company_id')
            ->where('identities.public_key', $pubKey)
            ->get($this->queryColumns);

        $companies = $members = $roles = [
            'ids'      => [],
            'entities' => []
        ];

        foreach ($identities as $identity) {
            $company = $identity->company();
            $member  = $identity->member();
            $role    = $identity->role();

            if ((! empty($company['id'])) && (! in_array($company, $companies['ids']))) {
                $company = $this->entityFactory->create(
                    'Company',
                    $company
                );

                $companies['ids'][$company->id] = $company;
                $companies['entities'][]        = $company;
            }

            if ((! empty($member['id'])) && (! in_array($member, $members['ids']))) {
                $member = $this->entityFactory->create(
                    'Company\Member',
                    $member
                );
                $members['ids'][$member->id] = $member;
                $members['entities'][]       = $member;
            }

            if ((! empty($role['name'])) && (! in_array($role, $roles['ids']))) {
                $role = $this->entityFactory->create(
                    'Role',
                    $role
                );
                $roles['ids'][$role->name] = $role;
                $roles['entities'][]       = $role;
            }
        }

        // populating members entities
        foreach ($members['entities'] as $member) {
            $member->relations['company'] = $companies['ids'][$member->company];
            $member->relations['role']    = $roles['ids'][$member->role];
        }

        $identity = $identities->first();

        if (! $identity) {
            throw new NotFound();
        }

        $identityCompanies = new Collection($companies['entities']);

        // populating identities available companies
        $identity->relations['member'] = new Collection($members['entities']);

        foreach ($identityCompanies as $key => $company) {
            $children          = $companyRepository->getChildrenById($company->id);
            $identityCompanies = $identityCompanies->merge($children->toArray());
        }

        $identity->relations['company'] = $identityCompanies->unique('slug')->values();

        return $identity;
    }

    /**
     * {@inheritdoc}
     */
    public function findOneBySourceNameAndProfileId(
        string $sourceName,
        string $profileId,
        string $appKey
    ) : Identity {
        return $this->findOneBy(
            [
                'reference' => $this->getReference(
                    $sourceName,
                    $profileId,
                    $appKey
                )
            ]
        );
    }
}
