<?php
/**
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace App\Repository;

use App\Exception\NotFound;
use App\Model\Company;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Database-based Company Repository Implementation.
 */
class DBCompany extends AbstractDBRepository implements CompanyInterface {
    /**
     * Class constructor.
     *
     * @param App\Model\Company $model
     *
     * @return void
     */
    public function __construct(Company $model) {
        $this->model = $model;
    }

    /**
     * {@inheritDoc}
     */
    public function findBySlug($slug) {
        try {
            return $this->model->where('slug', $slug)->firstOrFail();
        } catch (ModelNotFoundException $exception) {
            throw new NotFound(get_class($this->model));
        }
    }

    /**
     * {@inheritDoc}
     */
    public function findByPubKey($pubKey) {
        try {
            return $this->model->where('public_key', $pubKey)->firstOrFail();
        } catch (ModelNotFoundException $exception) {
            throw new NotFound(get_class($this->model));
        }
    }

    /**
     * {@inheritDoc}
     */
    public function findByPrivKey($privKey) {
        try {
            return $this->model->where('private_key', $privKey)->firstOrFail();
        } catch (ModelNotFoundException $exception) {
            throw new NotFound(get_class($this->model));
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getAllByParentId($parentId) {
        return $this->model->where('parent_id', $parentId)->get();
    }

    /**
     * {@inheritDoc}
     */
    public function deleteByParentId($parentId) {
        $this->model->where('parent_id', $parentId)->delete();
    }
}
