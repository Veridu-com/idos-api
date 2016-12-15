<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\HandlerService;

use App\Command\AbstractCommand;

/**
 * HandlerService "Delete all" Command.
 */
class DeleteAll extends AbstractCommand {
    /**
     * HandlerService's company.
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
     *
     * @return \App\Command\HandlerService\DeleteAll
     */
    public function setParameters(array $parameters) : self {
        return $this;
    }
}
