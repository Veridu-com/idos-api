<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\Service;

use App\Command\AbstractCommand;
use App\Command\CommandInterface;

/**
 * Service "Create new" Command.
 */
class CreateNew extends AbstractCommand {
    /**
     * Service's company.
     *
     * @var string
     */
    public $company;
    /**
     * Handler Service's service's id.
     *
     * @var string
     */
    public $handlerServiceId;
    /**
     * Service's listens attribute.
     *
     * @var string
     */
    public $listens;
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
        if (isset($parameters['decoded_handler_service_id'])) {
            $this->handlerServiceId = $parameters['decoded_handler_service_id'];
        }

        if (isset($parameters['listens'])) {
            $this->listens = $parameters['listens'];
        }

        return $this;
    }
}
