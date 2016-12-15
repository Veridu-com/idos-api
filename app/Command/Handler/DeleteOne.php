<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\Handler;

use App\Command\AbstractCommand;

/**
 * Handler "Delete one" Command.
 */
class DeleteOne extends AbstractCommand {
    /**
     * Handler's id.
     *
     * @var int
     */
    public $handlerId;
    /**
     * Acting company.
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
     * @return \App\Command\Handler\DeleteOne
     */
    public function setParameters(array $parameters) : self {
        return $this;
    }
}
