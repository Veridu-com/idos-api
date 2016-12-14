<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository;

use App\Entity\Company;
use App\Entity\Handler;
use App\Exception\NotFound;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;

/**
 * Database-based Handler Services Repository Implementation.
 */
class DBHandlerServices extends AbstractSQLDBRepository {
    /**
     * The table associated with the repository.
     *
     * @var string
     */
    protected $tableName = 'handler_services';
    /**
     * The entity associated with the repository.
     *
     * @var string
     */
    protected $entityName = 'HandlerService';
}
