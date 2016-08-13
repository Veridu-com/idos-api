<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Daemon;

/**
 * Database-based Daemon Repository Implementation.
 */
class DBDaemon extends AbstractDBRepository implements DaemonInterface {
    /**
     * The table associated with the repository.
     *
     * @var string
     */
    protected $tableName = 'daemons';

    /**
     * The entity associated with the repository.
     *
     * @var string
     */
    protected $entityName = 'Daemon';

    /**
     * {@inheritdoc}
     */
    protected $filterableKeys = [
        'name'    => 'string', 
        'slug'    => 'string', 
        'enabled' => 'boolean'
    ];


}
