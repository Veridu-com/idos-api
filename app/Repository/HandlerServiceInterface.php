<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository;

use App\Entity\HandlerService;
use Illuminate\Support\Collection;

/**
 * Handler Service Repository Interface.
 */
interface HandlerServiceInterface extends RepositoryInterface {

    /**
     * Return handler services based on their company id.
     *
     * @param int $companyId The company identifier
     *
     * @return \Illuminate\Support\Collection
     */
    public function getByCompanyId(int $companyId, array $queryParams) : Collection;
}
