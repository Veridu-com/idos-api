<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Service;
use Illuminate\Support\Collection;

/**
 * Service Repository Interface.
 */
interface ServiceInterface extends RepositoryInterface {
    /**
     * Retrieves all services.
     *
     * @param int    companyId setting's company_id
     * @param string section   setting's section
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll(array $queryParams = []) : Collection;
}
