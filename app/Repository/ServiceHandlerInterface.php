<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository;

use App\Entity\ServiceHandler;
use Illuminate\Support\Collection;

/**
 * ServiceHandler Repository Interface.
 */
interface ServiceHandlerInterface extends RepositoryInterface {
    /**
     * Returns a service handler based on its id and company id.
     *
     * @param int $serviceHandlerId
     * @param int $companyId
     *
     * @return \App\Entity\ServiceHandler
     */
    public function findOne(int $serviceHandlerId, int $companyId) : ServiceHandler;

    /**
     * Return service handlers based on its slug and company id.
     *
     * @param int    $companyId
     * @param string $serviceSlug
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getByService(int $companyId, string $serviceSlug) : Collection;

    /**
     * Return service handlers based on their company id.
     *
     * @param int $companyId The company identifier
     *
     * @return \Illuminate\Support\Collection
     */
    public function getByCompanyId(int $companyId) : Collection;

    /**
     * Gets all ServiceHandlers based on their Company Id.
     *
     * @param int $companyId
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getByServiceCompanyId(int $companyId) : Collection;

    /**
     * Deletes one setting based on their companyId, own slug and serviceSlug.
     *
     * @param int $companyId
     * @param int $serviceHandlerId
     *
     * @return int
     */
    public function deleteOne(int $companyId, int $serviceHandlerId) : int;

    /**
     * Deletes all ServiceHandlers based on their Company Id.
     *
     * @param int $companyId
     *
     * @return int
     */
    public function deleteByCompanyId(int $companyId) : int;
}
