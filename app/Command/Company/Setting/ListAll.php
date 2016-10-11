<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\Company\Setting;

use App\Command\AbstractCommand;

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
     * Acting Identity.
     *
     * @var \App\Entity\Identity
     */
    public $identity;

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
     *
     * @return \App\Command\Company\Setting\ListAll
     */
    public function setParameters(array $parameters) : self {

        return $this;
    }
}
