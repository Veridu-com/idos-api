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
 * Database-based Service Repository Implementation.
 */
class DBService extends AbstractDBRepository implements ServiceInterface {
    /**
     * The table associated with the repository.
     *
     * @var string
     */
    protected $tableName = 'services';
    /**
     * The entity associated with the repository.
     *
     * @var string
     */
    protected $entityName = 'Service';
    
}
