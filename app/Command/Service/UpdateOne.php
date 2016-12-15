<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\Service;

use App\Command\AbstractCommand;

/**
 * Service "Update one" Command.
 */
class UpdateOne extends AbstractCommand {
    /**
     * Service's id.
     *
     * @var int
     */
    public $serviceId;
    /**
     * Service's company.
     *
     * @var \App\Entity\Company
     */
    public $company;
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
     *
     * @return \App\Command\Service\UpdateOne
     */
    public function setParameters(array $parameters) : self {
        if (isset($parameters['listens'])) {
            $this->listens = $parameters['listens'];
        }

        return $this;
    }
}
