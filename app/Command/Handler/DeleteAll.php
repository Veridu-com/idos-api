<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\Handler;

use App\Command\AbstractCommand;
use App\Command\CommandInterface;

/**
 * Handler "Delete all" Command.
 */
class DeleteAll extends AbstractCommand {
    /**
     * Handler's company.
     *
     * @var \App\Entity\Company
     */
    public $company;
    /**
     * Identity.
     *
     * @var \App\Entity\Identity
     */
    public $identity;

    /**
     * {@inheritdoc}
     */
    public function setParameters(array $parameters) : CommandInterface {
        return $this;
    }
}
