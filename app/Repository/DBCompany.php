<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository;

use App\Entity\Company;
use App\Entity\Company\Member;
use App\Entity\Identity;
use App\Entity\Role;
use App\Exception\AppException;
use Illuminate\Support\Collection;

/**
 * Database-based Company Repository Implementation.
 */
class DBCompany extends AbstractSQLDBRepository implements CompanyInterface {
    /**
     * The table associated with the repository.
     *
     * @var string
     */
    protected $tableName = 'companies';
    /**
     * The entity associated with the repository.
     *
     * @var string
     */
    protected $entityName = 'Company';

    /**
     * {@inheritdoc}
     */
    public function isParent(Company $parent, Company $child) : bool {
        if ($child->parentId === null) {
            return false;
        }

        if ($child->parentId === $parent->id) {
            return true;
        }

        return $this->isParent(
            $this->find($child->parentId),
            $child
        );
    }

    /**
     * {@inheritdoc}
     */
    public function findByPubKey(string $pubKey) : Company {
        return $this->findOneBy(['public_key' => $pubKey]);
    }

    /**
     * {@inheritdoc}
     */
    public function findBySlug(string $slug) : Company {
        return $this->findOneBy(['slug' => $slug]);
    }

    /**
     * {@inheritdoc}
     */
    public function getByParentId(int $parentId) : Collection {
        return $this->findBy(['parent_id' => $parentId]);
    }

    /**
     * {@inheritdoc}
     */
    public function saveNewCompany(Company $company, Identity $owner) : Company {
        $company = parent::save($company);
        $this->createNewMember($company, $owner, Role::COMPANY_OWNER);

        return $company;
    }

    /**
     * {@inheritdoc}
     */
    public function createNewMember(Company $company, Identity $identity, string $role) : Member {
        $query = $this->query('members', Member::class);
        $id    = $query->insertGetId(
            [
                'company_id'  => $company->id,
                'identity_id' => $identity->id,
                'role'        => $role
            ]
        );
        if ($id) {
            $member = $this->entityFactory->create(
                'Company\Member',
                [
                    'role'     => $role,
                    'company'  => $company->id,
                    'identity' => $identity->id,
                ]
            );
            $member->relations['company']  = $company;
            $member->relations['identity'] = $identity;

            return $member;
        }

        throw new AppException(sprintf('Error creating Company Member on %s', get_class($this)), 500);
    }

    /**
     * {@inheritdoc}
     */
    public function delete(int $id, string $key = 'id') : int {
        return $this->deleteBy([$key => $id]);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteByParentId(int $parentId) : int {
        return $this->deleteBy(['parent_id' => $parentId]);
    }
}
