<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types=1);

namespace App\Command\CompanyServiceHandler;

use App\Command\AbstractCommand;

/**
 * CompanyServiceHandler "Create New" Command.
 */
class CreateNew extends AbstractCommand {
    /**
     * CompanyServiceHandler's company's Id.
     *
     * @var int
     */
    public $companyId;

    /**
     * CompanyServiceHandler's service handler's Id.
     *
     * @var int
     */
    public $serviceHandlerId;

    /**
     * {@inheritdoc}
     *
     * @return App\Command\CompanyServiceHandler\CreateNew
     */
    public function setParameters(array $parameters) : self {
        if (isset($parameters['service_handler_id'])) {
            $this->serviceHandlerId = $parameters['service_handler_id'];
        }

        return $this;
    }
}
