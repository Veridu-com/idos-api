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
     * Return handlers based on their company id.
     *
     * @param int   $companyId   The company identifier
     * @param array $queryParams The query parameters
     *
     * @return \Illuminate\Support\Collection
     */
    public function getByCompanyId(int $companyId, array $queryParams = []) : Collection;

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
     * @param int                 $handlerId The handler identifier
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
