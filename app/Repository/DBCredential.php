<?php
/**
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace App\Repository;

use App\Exception\NotFound;
use App\Model\Credential;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Database-based Credential Repository Implementation.
 */
class DBCredential extends AbstractDBRepository implements CredentialInterface {
    /**
     * Class constructor.
     *
     * @param App\Model\Credential $model
     *
     * @return void
     */
    public function __construct(Credential $model) {
        $this->model = $model;
    }

    /**
     * {@inheritDoc}
     */
    public function findByPubKey($pubKey) {
        try {
            return $this->model->where('public', $pubKey)->firstOrFail();
        } catch (ModelNotFoundException $exception) {
            throw new NotFound(get_class($this->model));
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getAllByCompanyId($companyId) {
        return $this->model->where('company_id', $companyId)->get();
    }
}
