<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository;

use App\Entity\Member;
use App\Exception\NotFound;
use Illuminate\Support\Collection;

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
    protected $entityName = 'Member';

    /**
     * {@inheritdoc}
     */
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
            'type' => 'MANY_TO_ONE',
            'table' => 'users',
            'foreignKey' => 'user_id',
            'key' => 'id',
            'entity' => 'User',
            'hydrate' => [
                'username',
                'created_at'
            ]
        ],
    ];

    /**
     * {@inheritdoc}
     */
    public function getAllByCompanyId(int $companyId) : Collection {
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

        return $this->castHydrate($items);
    }

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
        return $this->findOneBy([
            'id' => $memberId
        ]);
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
