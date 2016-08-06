<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types=1);

namespace App\Repository;

use App\Entity\CompanyServiceHandler;
use Illuminate\Support\Collection;

/**
 * CompanyServiceHandler Repository Interface.
 */
interface CompanyServiceHandlerInterface extends RepositoryInterface {
    /**
     * Gets all CompanyServiceHandlers based on their Company Id.
     *
     * @param int $companyId
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllByCompanyId(int $companyId) : Collection;
    
    /**
     * Deletes all CompanyServiceHandlers based on their Company Id.
     *
     * @param int $companyId
     *
     * @return int
     */
    public function deleteByCompanyId(int $companyId) : int;

    /**
     * Find one setting based on its id.
     *
     * @param int    $companyId
     * @param string $slug
     * @param string $serviceSlug
     *
     * @return App\Entity\CompanyServiceHandler
     */
    public function findOne(int $id, int $companyId) : CompanyServiceHandler;

    /**
     * Deletes one setting based on their companyId, own slug and serviceSlug.
     *
     * @param int    $companyId
     * @param string $slug
     * @param string $serviceSlug
     *
     * @return int
     */
    public function deleteOne(int $id, int $companyId) : int ;
}
