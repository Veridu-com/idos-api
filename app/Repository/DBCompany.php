<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository;

use App\Entity\Company;
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
    public function findBySlug(string $slug) : Company {
        return $this->findOneBy(['slug' => $slug]);
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
    public function getAllByParentId(int $parentId) : Collection {
        return $this->findBy(['parent_id' => $parentId]);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteByParentId(int $parentId) : int {
        return $this->deleteBy(['parent_id' => $parentId]);
    }

    /**
     * {@inheritdoc}
     */
    public function delete(int $id, string $key = 'id') : int {
        return $this->deleteBy([$key => $id]);
    }

    /**
     * Determines if parent.
     *
     * @param \App\Entity\Company $parent The parent
     * @param \App\Entity\Company $child  The child
     *
     * @return bool True if parent, False otherwise.
     */
    public function isParent(Company $parent, Company $child) {
        if ($child->parentId === null) {
            return false;
        }

        if ($child->parentId === $parent->id) {
            return true;
        }

        $parent = $this->find($child->parentId);

        return $this->isParent($parent, $child);
    }
}
