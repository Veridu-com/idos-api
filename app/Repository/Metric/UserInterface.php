<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository\Metric;

use App\Entity\Company;
use App\Repository\RepositoryInterface;
use Illuminate\Support\Collection;

/**
 * User Metrics Repository Interface.
 */
interface UserInterface extends RepositoryInterface {
    /**
     * Return user metrics.
     *
     * @param \App\Entity\Company $company
     * @param int                 $from
     * @param int                 $to
     * @param array               $queryParams
     *
     * @return \Illuminate\Support\Collection
     */
    public function getByCompanyAndDateInterval(Company $company, int $from = null, int $to = null, array $queryParams = []) : Collection;
}
