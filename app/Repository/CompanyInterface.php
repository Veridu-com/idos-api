<?php
/**
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace App\Repository;

/**
 * Company Repository Interface.
 */
interface CompanyInterface extends RepositoryInterface {
    /**
     * Finds an entity based on its Public Key.
     *
     * @param string $pubKey
     *
     * @throws App\Exception\NotFound
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function findByPubKey($pubKey);
    /**
     * Finds an entity based on its Private Key.
     *
     * @param string $privKey
     *
     * @throws App\Exception\NotFound
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function findByPrivKey($privKey);
    /**
     * Finds an entity based on its Slug.
     *
     * @param string $slug
     *
     * @throws App\Exception\NotFound
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function findBySlug($slug);
    /**
     * Gets all entities based on their Parent Id.
     *
     * @param int $parentId
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllByParentId($parentId);
    /**
     * Deletes all entities based on their Parent Id.
     *
     * @param int $parentId
     *
     * @return void
     */
    public function deleteByParentId($parentId);
}
