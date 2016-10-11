<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository;

use App\Entity\Company;
use App\Entity\Service;
use Illuminate\Support\Collection;

/**
 * Service Repository Interface.
 */
interface ServiceInterface extends RepositoryInterface {
    /**
     * Find one Service.
     *
     * @param int                 $serviceId The service identifier
     * @param \App\Entity\Company $company   The company
     *
     * @throws \App\Exception\NotFound
     *
     * @return \App\Entity\Service
     */
    public function findOne(int $serviceId, Company $company) : Service;

    /**
     * Return services based on their company.
     *
     * @param \App\Entity\Company $company     The company
     * @param array               $queryParams The query parameters
     *
     * @return \Illuminate\Support\Collection
     */
    public function getByCompany(Company $company, array $queryParams = []) : Collection;

    /**
     * Finds a Service based on its Public Key.
     *
     * @param string $pubKey
     *
     * @throws \App\Exception\NotFound
     *
     * @return \App\Entity\Service
     */
    public function findByPubKey(string $pubKey) : Service;

    /**
     * Deletes one Service.
     *
     * @param int                 $serviceId The service identifier
     * @param \App\Entity\Company $company   The company
     *
     * @throws \App\Exception\NotFound
     *
     * @return int
     */
    public function deleteOne(int $serviceId, Company $company) : int;

    /**
     * Deletes all Services that belongs to the Company.
     *
     * @param int $companyId The company identifier
     *
     * @throws \App\Exception\NotFound
     *
     * @return int Number of deleted rows
     */
    public function deleteByCompanyId(int $companyId) : int;
}
