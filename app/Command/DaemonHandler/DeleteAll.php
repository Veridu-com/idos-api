<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types=1);

namespace App\Command\DaemonHandler;

use App\Command\AbstractCommand;

/**
 * DaemonHandler "Delete all" Command.
 */
class DeleteAll extends AbstractCommand {
    /**
     * DaemonHandler company's Id.
     *
     * @var int
     */
    public $companyId;

    /**
     * {@inheritdoc}
     *
     * @return App\Command\DaemonHandler\DeleteAll
     */
    public function setParameters(array $parameters) : self {
        if (isset($parameters['companyId'])) {
            $this->companyId = $parameters['companyId'];
        }

        return $this;
    }
}
