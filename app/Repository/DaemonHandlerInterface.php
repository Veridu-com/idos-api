<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types=1);

namespace App\Repository;

use App\Entity\DaemonHandler;
use Illuminate\Support\Collection;

/**
 * DaemonHandler Repository Interface.
 */
interface DaemonHandlerInterface extends RepositoryInterface {
    /**
     * Gets all DaemonHandlers based on their Company Id.
     *
     * @param int $companyId
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllByCompanyId(int $companyId) : Collection;

    /**
     * Gets all DaemonHandlers based on their Company Id and daemon's slug.
     *
     * @param int    $companyId
     * @param string $daemonSlug
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function findAllFromDaemon(int $companyIserv, string $daemonSlug) : Collection;

    /**
     * Deletes all DaemonHandlers based on their Company Id.
     *
     * @param int $companyId
     *
     * @return int
     */
    public function deleteByCompanyId(int $companyId) : int;

    /**
     * Find one setting based on their companyId, daemonSlug and own slug.
     *
     * @param int $companyId
     * @param int $daemonHandlerId
     *
     * @return App\Entity\DaemonHandler
     */
    public function findOne(int $companyId, int $daemonHandlerId) : DaemonHandler;

    /**
     * Deletes one setting based on their companyId, own slug and daemonSlug.
     *
     * @param int    $companyId
     * @param string $slug
     * @param string $daemonSlug
     *
     * @return int
     */
    public function deleteOne(int $companyId, string $slug, string $daemonSlug) : int;
}
