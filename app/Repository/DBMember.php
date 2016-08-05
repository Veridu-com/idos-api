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
 * Database-based Member Repository Implementation.
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
    public function getAllByCompanyId(int $companyId) : Collection {
        return $this->findBy(['company_id' => $companyId]);
    }

     /**
      * {@inheritdoc}
      */
     public function getAllByCompanyIdAndRole(int $companyId, array $roles) : Collection {
        $items = new Collection();
        foreach ($roles as $role) {
            $items = $items->merge($this->findBy(['company_id' => $companyId, 'role' => $role]));
        }

        return $items;
    }
    /**
     * {@inheritdoc}
     */
    public function findOne(int $companyId, int $userId) : Member {
        return $this->findOneBy([
            'company_id'  => $companyId,
            'user_id'     => $userId
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
}
