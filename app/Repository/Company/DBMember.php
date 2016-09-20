<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository\Company;

use App\Entity\Company\Member;
use Illuminate\Support\Collection;
use App\Repository\AbstractSQLDBRepository;

/**
 * Database-based Member Repository Implementation.
 */
class DBMember extends AbstractSQLDBRepository implements MemberInterface {
    /**
     * The table associated with the repository.
     *
     * @var string
     */
    protected $tableName = 'members';
    /**
     * The entity associated with the repository.
     *
     * @var string
     */
    protected $entityName = 'Company\Member';

    private $queryColumns = [
        'members.*',
        'companies.updated_at as company.updated_at',
        'identities.id as identity.id',
        'identities.reference as identity.reference',
        'identities.public_key as identity.public_key',
        'identities.private_key as identity.private_key',
        'identities.created_at as identity.created_at',
        'roles.id as role.id',
        'roles.name as role.name',
        'roles.rank as role.rank',
        'roles.bit as role.bit',
        'roles.created_at as role.created_at'
    ];

    /**
     * {@inheritdoc}
     */
    public function getAllByCompanyId(int $companyId, array $queryParams = []) : Collection {
        $members = $this->query()
            ->join('identities', 'identities.id', '=', 'members.identity_id')
            ->join('roles', 'roles.name', '=', 'members.role')
            ->join('companies', 'companies.id', '=', 'members.company_id')
            ->where('members.company_id', '=', $companyId)
            ->get($this->queryColumns);

        return $this->castHydrate($members);
    }
    /*public function getAllByCompanyId(int $companyId) : Collection {
        $items = new Collection();
        $items = $items->merge(
            $this->query()
                ->join('users', 'users.id', '=', 'members.user_id')
                ->where('members.company_id', '=', $companyId)
                ->get(
                    [
                        'users.username as user.username',
                        'users.created_at as user.created_at',
                        'members.*'
                    ]
                )
        );

        return $this->castHydrate($members);
    }*/

    protected $filterableKeys = [
        /*'source.id' => 'decoded',
        'source.name' => 'string',
        'creator' => 'string',
        'name' => 'string',
        'type' => 'string',
        'created_at' => 'date'*/
    ];

    /**
     * {@inheritdoc}
     */
    protected $relationships = [
        'user' => [
            'type'       => 'MANY_TO_ONE',
            'table'      => 'users',
            'foreignKey' => 'user_id',
            'key'        => 'id',
            'entity'     => 'User',
            'hydrate'    => [
                'username',
                'created_at'
            ]
        ],
    ];

    /**
     * {@inheritdoc}
     */
    public function getAllByCompanyIdAndRole(int $companyId, array $roles) : Collection {
        $items = new Collection();
        $items = $items->merge(
            $this->query()
                ->join('users', 'users.id', '=', 'members.user_id')
                ->where('members.company_id', '=', $companyId)
                ->whereIn('members.role', $roles)
                ->get(
                    ['users.username as user.username',
                    'users.created_at as user.created_at',
                    'members.*']
                )
        );

        return $this->castHydrate($items);
    }

    /**
     * {@inheritdoc}
     */
    public function findOne(int $memberId) : Member {
        return $this->findOneBy(
            [
            'id' => $memberId
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function deleteOne(int $companyId, int $userId) : int {
        return $this->query()
            ->where('company_id', $companyId)
            ->where('user_id', $userId)
            ->delete();
    }

    /**
     * {@inheritdoc}
     */
    public function deleteByCompanyId(int $companyId) : int {
        return $this->deleteByKey('company_id', $companyId);
    }

    /**
     * {@inheritdoc}
     */
    public function saveOne(Member $member) : Member {
        $user                      = $member->relations['user'];
        $member                    = $this->save($member);
        $member->relations['user'] = $user;

        return $member;
    }
}
