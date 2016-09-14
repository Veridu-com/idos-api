<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository;

use App\Entity\Identity;
use Illuminate\Support\Collection;

/**
 * Database-based Identity Repository Implementation.
 */
class DBIdentity extends AbstractSQLDBRepository implements IdentityInterface {
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

    /**
     * {@inheritdoc}
     */
    public function findByPubKey(string $pubKey) : Identity {
        $identities = $this->query()
            ->join('members', 'members.identity_id', 'identities.id')
            ->join('roles', 'members.role', 'roles.name')
            ->join('companies', 'companies.id', 'members.company_id')
            ->where('identities.public_key', $pubKey)
            ->get($this->queryColumns);

        $companies = $members =  $roles = [
            'ids' => [],
            'entities' => []
        ];

        foreach ($identities as $identity) {
            $company = $identity->company();
            $member = $identity->member();
            $role = $identity->role();


            if (! array_has($companies['ids'], $company['id'])) {
                $company =  $this->entityFactory->create(
                    'Company',
                    $company
                );

                $companies['ids'][$company->id] = $company;
                array_push($companies['entities'], $company);
             }

            if (! array_has($members['ids'], $member['id'])) {
                $member =  $this->entityFactory->create(
                    'Member',
                    $member
                );
                $members['ids'][$member->id] = $member;
                array_push($members['entities'], $member);
             }

            if (! array_has($roles['ids'], $role['name'])) {
                $role =  $this->entityFactory->create(
                    'Role',
                    $role
                );
                $roles['ids'][$role->name] = $role;
                array_push($roles['entities'], $role);
             }
        }

        // populating members entities
        foreach ($members['entities'] as $member) {
            $member->relations['company'] = $companies['ids'][$member->company];
            $member->relations['role'] = $roles['ids'][$member->role];
        }

        $identity = $identities->first();

        // populating identities available companies
        $identity->relations['company'] = new Collection($companies['entities']);
        $identity->relations['member'] = new Collection($members['entities']);

        return $identity;
    }

    /**
     * {@inheritdoc}
     */
    public function findBySourceNameAndProfileId(string $sourceName, string $profileId, string $applicationId) : Collection {
        $reference = sprintf(
            '%s:%s',
            $sourceName,
            $profileId, 
            $applicationId
        );

        return $this->findBy(['reference' => md5($reference)]);
    }
}
