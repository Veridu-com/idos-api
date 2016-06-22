<?php
/**
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace App\Repository;

use App\Exception\NotFound;
use Illuminate\Support\Collection;

/**
 * Array-based Credential Repository Implementation.
 */
class ArrayCredential extends AbstractArrayRepository implements CredentialInterface {
    /**
     * {@inheritDoc}
     */
    public function findByPubKey($pubKey) {
        foreach ($this->storage as $item)
            if ($item->public_key === $pubKey)
                return $item;
        throw new NotFound();
    }

    /**
     * {@inheritDoc}
     */
    public function getAllByCompanyId($companyId) {
        $return = [];
        foreach ($this->storage as $item)
            if ($item->company_id === $companyId)
                $return[] = $item;

        return new Collection($return);
    }
}
