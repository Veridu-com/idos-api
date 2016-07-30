<?php

/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository;

use App\Entity\Company as CompanyEntity;
use Illuminate\Support\Collection;

/**
 * Company Repository Interface.
 */
interface CompanyInterface extends RepositoryInterface {
    /**
     * Finds a Company based on its Public Key.
     *
     * @param string $pubKey
     *
     * @throws App\Exception\NotFound
     *
     * @return App\Entity\Company
     */
    public function findByPubKey($pubKey) : CompanyEntity;
    /**
     * Finds a Company based on its Private Key.
     *
     * @param string $privKey
     *
     * @throws App\Exception\NotFound
     *
     * @return App\Entity\Company
     */
    public function findByPrivKey($privKey) : CompanyEntity;
    /**
     * Finds a Company based on its Slug.
     *
     * @param string $slug
     *
     * @throws App\Exception\NotFound
     *
     * @return App\Entity\Company
     */
    public function findBySlug($slug) : CompanyEntity;
    /**
     * Gets all Companies based on their Parent Id.
     *
     * @param int $parentId
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllByParentId($parentId) : Collection;
    /**
     * Deletes all Companies based on their Parent Id.
     *
     * @param int $parentId
     *
     * @return int
     */
    public function deleteByParentId(int $parentId) : int;
}
