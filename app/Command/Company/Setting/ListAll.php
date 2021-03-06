<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\Company\Setting;

use App\Command\AbstractCommand;
use App\Command\CommandInterface;

/**
 * Setting "List All" Command.
 */
class ListAll extends AbstractCommand {
    /**
     * Target Company.
     *
     * @var \App\Entity\Company
     */
    public $company;
    /**
     * Query parameters.
     *
     * @var array
     */
    public $queryParams;
    /**
     * Parent access.
     *
     * @var bool
     */
    public $hasParentAccess;

    /**
     * {@inheritdoc}
     */
    public function setParameters(array $parameters) : CommandInterface {
        return $this;
    }
}
