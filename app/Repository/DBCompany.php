<?php

/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Company;
use Illuminate\Support\Collection;

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
    public function findBySlug($slug) : Company {
        return $this->findOneBy(['slug' => $slug]);
    }

    /**
     * {@inheritdoc}
     */
    public function findByPubKey($pubKey) : Company {
        return $this->findOneBy(['public_key' => $pubKey]);
    }

    /**
     * {@inheritdoc}
     */
    public function findByPrivKey($privKey) : Company {
        return $this->findOneBy(['private_key' => $privKey]);
    }

    /**
     * {@inheritdoc}
     */
    public function getAllByParentId($parentId) : Collection {
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
        return $this->deleteBy(['id' => $id]);
    }
}
