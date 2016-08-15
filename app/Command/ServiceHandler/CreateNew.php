<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types=1);

namespace App\Command\ServiceHandler;

use App\Command\AbstractCommand;

/**
 * ServiceHandler "Create new" Command.
 */
class CreateNew extends AbstractCommand {
    /**
     * ServiceHandler's company's id.
     *
     * @var string
     */
    public $companyId;

    /**
     * ServiceHandler's service's id.
     *
     * @var string
     */
    public $serviceId;

    /**
     * ServiceHandler's listens attribute.
     *
     * @var string
     */
    public $listens;

    /**
     * {@inheritdoc}
     *
     * @return App\Command\ServiceHandler\CreateNew
     */
    public function setParameters(array $parameters) : self {
        if (isset($parameters['decoded_service_id'])) {
            $this->serviceId = $parameters['decoded_service_id'];
        }

        if (isset($parameters['listens'])) {
            $this->listens = $parameters['listens'];
        }

        return $this;
    }
}
