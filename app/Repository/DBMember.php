<?php

/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Member;
use Illuminate\Support\Collection;

/**
 * Database-based Credential Repository Implementation.
 */
class DBMember extends AbstractDBRepository implements MemberInterface {
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
    public function getAllByCompanyId($companyId) : Collection {
        return $this->findBy(['company_id' => $companyId]);
    }

    /**
    * {@inheritdoc}
    */
    public function getAllByCompanyIdAndRole($companyId, $roles) : Collection {
        $items = [];
        foreach ($roles as $role) {
            $items = array_merge($this->findBy(['company_id' => $companyId, 'role' => $role])->toArray(), $items);
        }

        return new Collection($items);
    }
    /**
     * {@inheritdoc}
     */
    public function findOne($companyId, $username) : Member {
        return $this->findOneBy([
            'company_id' => $companyId,
            'username'    => $username
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteOne(int $companyId, string $username) : int {
        return $this->query()
            ->where('company_id', $companyId)
            ->where('username', $username)
            ->delete();
    }

    /**
    * {@inheritdoc}
    */
    public function deleteByCompanyId($companyId) : int {
        return $this->deleteByKey('company_id', $companyId);
    }
}
