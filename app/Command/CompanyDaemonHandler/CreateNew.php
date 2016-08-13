<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types=1);

namespace App\Command\CompanyDaemonHandler;

use App\Command\AbstractCommand;

/**
 * CompanyDaemonHandler "Create New" Command.
 */
class CreateNew extends AbstractCommand {
    /**
     * CompanyDaemonHandler's company's Id.
     *
     * @var int
     */
    public $companyId;

    /**
     * CompanyDaemonHandler's daemon handler's Id.
     *
     * @var int
     */
    public $daemonHandlerId;

    /**
     * {@inheritdoc}
     *
     * @return App\Command\CompanyDaemonHandler\CreateNew
     */
    public function setParameters(array $parameters) : self {
        if (isset($parameters['daemon_handler_id'])) {
            $this->daemonHandlerId = $parameters['daemon_handler_id'];
        }

        return $this;
    }
}
