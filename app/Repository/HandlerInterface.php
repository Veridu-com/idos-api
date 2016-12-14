<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository;

use App\Entity\Company;
use App\Entity\Handler;
use Illuminate\Support\Collection;

/**
 * Handler Repository Interface.
 */
interface HandlerInterface extends RepositoryInterface {
    /**
     * Find one Handler.
     *
     * @param int                 $handlerId The service identifier
     * @param \App\Entity\Company $company   The company
     *
     * @throws \App\Exception\NotFound
     *
     * @return \App\Entity\Handler
     */
    public function findOne(int $handlerId, Company $company) : Handler;

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
     * Finds a Handler based on its Public Key.
     *
     * @param string $pubKey
     *
     * @throws \App\Exception\NotFound
     *
     * @return \App\Entity\Handler
     */
    public function findByPubKey(string $pubKey) : Handler;

    /**
     * Deletes one Handler.
     *
     * @param int                 $handlerId The service identifier
     * @param \App\Entity\Company $company   The company
     *
     * @throws \App\Exception\NotFound
     *
     * @return int
     */
    public function deleteOne(int $handlerId, Company $company) : int;

    /**
     * Deletes all Handlers that belongs to the Company.
     *
     * @param int $companyId The company identifier
     *
     * @throws \App\Exception\NotFound
     *
     * @return int Number of deleted rows
     */
    public function deleteByCompanyId(int $companyId) : int;
}
