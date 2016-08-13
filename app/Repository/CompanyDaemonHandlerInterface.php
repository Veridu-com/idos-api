<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types=1);

namespace App\Repository;

use App\Entity\CompanyDaemonHandler;
use Illuminate\Support\Collection;

/**
 * CompanyDaemonHandler Repository Interface.
 */
interface CompanyDaemonHandlerInterface extends RepositoryInterface {
    /**
     * Gets all CompanyDaemonHandlers based on their Company Id.
     *
     * @param int $companyId
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllByCompanyId(int $companyId) : Collection;

    /**
     * Deletes all CompanyDaemonHandlers based on their Company Id.
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
     * @param string $daemonSlug
     *
     * @return App\Entity\CompanyDaemonHandler
     */
    public function findOne(int $id, int $companyId) : CompanyDaemonHandler;

    /**
     * Deletes one setting based on their companyId and $id.
     *
     * @param int    $companyId
     *
     * @return int
     */
    public function deleteOne(int $id, int $companyId) : int;
}
