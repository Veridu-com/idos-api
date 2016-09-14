<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository;

use App\Entity\Company;
use App\Entity\Identity;
use App\Entity\Member;
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
    public function findByPubKey(string $pubKey) : Company;

    /**
     * Finds a Company based on its Slug.
     *
     * @param string $slug
     *
     * @throws App\Exception\NotFound
     *
     * @return App\Entity\Company
     */
    public function findBySlug(string $slug) : Company;

    /**
     * Creats a new member for the company.
     *
     * @param \App\Entity\Company  $company  The company
     * @param \App\Entity\Identity $identity The identity
     * @param string               $role     The role
     */
    public function newMember(Company $company, Identity $identity, string $role) : Member;

    /**
     * Gets all Companies based on their Parent Id.
     *
     * @param int $parentId
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllByParentId(int $parentId) : Collection;

    /**
     * Deletes all Companies based on their Parent Id.
     *
     * @param int $parentId
     *
     * @return int
     */
    public function deleteByParentId(int $parentId) : int;

    /**
     * Determines if a company is related to another.
     *
     * @param \App\Entity\Company $parent The parent
     * @param \App\Entity\Company $child  The child
     *
     * @return bool
     */
    public function isParent(Company $parent, Company $child) : bool;
}
