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
     * Gets all ServiceHandlers based on their Company Id.
     *
     * @param int $companyId
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllByCompanyId(int $companyId) : Collection;

    /**
     * Gets all ServiceHandlers based on their Company Id and service's slug.
     *
     * @param int    $companyId
     * @param string $serviceSlug
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function findAllFromService(int $companyId, string $serviceSlug) : Collection;

    /**
     * Deletes all ServiceHandlers based on their Company Id.
     *
     * @param int $companyId
     *
     * @return int
     */
    public function deleteByCompanyId(int $companyId) : int;

    /**
     * Find one setting based on their companyId, serviceSlug and own slug.
     *
     * @param int $companyId
     * @param int $serviceHandlerId
     *
     * @return App\Entity\ServiceHandler
     */
    public function findOne(int $companyId, int $serviceHandlerId) : ServiceHandler;

    /**
     * Deletes one setting based on their companyId, own slug and serviceSlug.
     *
     * @param int $companyId
     * @param int $serviceHandlerId
     *
     * @return int
     */
    public function deleteOne(int $companyId, int $serviceHandlerId) : int;
}
