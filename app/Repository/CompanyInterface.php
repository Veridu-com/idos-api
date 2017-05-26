<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository;

use App\Entity\Company;
use App\Entity\Company\Member;
use App\Entity\Identity;
use Illuminate\Support\Collection;

/**
 * Company Repository Interface.
 */
interface CompanyInterface extends RepositoryInterface {
    /**
     * Determines if a company is related to another.
     *
     * @param \App\Entity\Company $parent The parent
     * @param \App\Entity\Company $child  The child
     *
     * @return bool
     */
    public function isParent(Company $parent, Company $child) : bool;

    /**
     * Gets the children recursively, by company identifier.
     *
     * @param int $companyId The company identifier
     *
     * @throws \App\Exception\AppException
     *
     * @return \Illuminate\Support\Collection
     */
    public function getChildrenById(int $companyId) : Collection;

    /**
     * Returns a company based on its public key.
     *
     * @param string $pubKey
     *
     * @throws \App\Exception\NotFound
     *
     * @return \App\Entity\Company
     */
    public function findByPubKey(string $pubKey) : Company;

    /**
     * Returns a company based on its slug.
     *
     * @param string $slug
     *
     * @throws \App\Exception\NotFound
     *
     * @return \App\Entity\Company
     */
    public function findBySlug(string $slug) : Company;

    /**
     * Return companies based on its parent id.
     *
     * @param int $parentId
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getByParentId(int $parentId) : Collection;

    /**
     * Creates a new member for a company.
     *
     * @param \App\Entity\Company  $company  The company
     * @param \App\Entity\Identity $identity The identity
     * @param string               $role     The role
     *
     * @return \App\Entity\Company\Member
     */
    public function createNewMember(Company $company, Identity $identity, string $role) : Member;

    /**
     * Delete companies based on its parent id.
     *
     * @param int $parentId
     *
     * @return int
     */
    public function deleteByParentId(int $parentId) : int;
}
