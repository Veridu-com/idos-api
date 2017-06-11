<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository\Company;

use App\Entity\Company\Widget;
use App\Repository\RepositoryInterface;
use Illuminate\Support\Collection;

/**
 * Widget Repository Interface.
 */
interface WidgetInterface extends RepositoryInterface {
    /**
     * Gets Widgets based on their Company Id.
     *
     * @param int $companyId
     *
     * @return \Illuminate\Support\Collection
     */
    public function getByCompanyId(int $companyId) : Collection;

    /**
     * Finds a Widget by its hash property.
     *
     * @param string $hash
     *
     * @return \App\Entity\Company\Widget
     */
    public function findByHash(string $hash) : Widget;
}
