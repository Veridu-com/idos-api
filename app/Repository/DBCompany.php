<?php
/**
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace App\Repository;

use App\Exception\NotFound;
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
    protected $entityName = Company::class;

    /**
     * {@inheritDoc}
     */
    public function findBySlug($slug) {
        $result = $this->query()
            ->where('slug', $slug)
            ->first();
        if (empty($result))
            throw new NotFound();

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function findByPubKey($pubKey) {
        $result = $this->query()
            ->where('public_key', $pubKey)
            ->first();
        if (empty($result))
            throw new NotFound();

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function findByPrivKey($privKey) {
        $result = $this->query()
            ->where('private_key', $privKey)
            ->first();
        if (empty($result))
            throw new NotFound();

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function getAllByParentId($parentId) {
        return new Collection(
            $this->query()
                ->where('parent_id', $parentId)
                ->get()
        );
    }

    /**
     * {@inheritDoc}
     */
    public function deleteByParentId($parentId) {
        return $this->query()
            ->where('parent_id', $parentId)
            ->delete();
    }
}
