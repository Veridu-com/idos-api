<?php
/**
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace App\Repository;

use App\Entity\Company;

/**
 * Database-based Company Repository Implementation.
 */
class DBCompany extends AbstractDBRepository implements CompanyInterface {
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
    public function findBySlug($slug) {
        return $this->findByKey('slug', $slug);
    }

    /**
     * {@inheritdoc}
     */
    public function findByPubKey($pubKey) {
        return $this->findByKey('public_key', $pubKey);
    }

    /**
     * {@inheritdoc}
     */
    public function findByPrivKey($privKey) {
        return $this->findByKey('private_key', $privKey);
    }

    /**
     * {@inheritdoc}
     */
    public function getAllByParentId($parentId) {
        return $this->getAllByKey('parent_id', $parentId);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteByParentId($parentId) {
        return $this->deleteByKey('parent_id', $parentId);
    }
}
