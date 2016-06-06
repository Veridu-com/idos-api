<?php
/**
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace App\Repository;

use App\Exception\NotFound;
use App\Model\Credential;

/**
 * Array-based Credential Repository Implementation.
 */
class ArrayCredential extends AbstractArrayRepository implements CredentialInterface {
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
        foreach ($this->storage as $item)
            if ($item->public_key === $pubKey)
                return $item;
        throw new NotFound(get_class($this->model));
    }

    /**
     * {@inheritDoc}
     */
    public function getAllByCompanyId($companyId) {
        $return = [];
        foreach ($this->storage as $item)
            if ($item->company_id === $companyId)
                $return[] = $item;

        return $this->model->newCollection($return);
    }
}
