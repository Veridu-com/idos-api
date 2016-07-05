<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace App\Repository;

/**
 * Setting Repository Interface.
 */
interface SettingInterface extends RepositoryInterface {
    /**
     * Finds a Setting based on its Public Key.
     *
     * @param string $pubKey
     *
     * @throws App\Exception\NotFound
     *
     * @return \App\Entity\Setting
     */
    public function findByPubKey($pubKey);
    /**
     * Gets all Settings based on their Company Id.
     *
     * @param int $companyId
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllByCompanyId($companyId);
    /**
     * Deletes all Settings based on their Company Id.
     *
     * @param int $companyId
     *
     * @return int
     */
    public function deleteByCompanyId($companyId);
}
