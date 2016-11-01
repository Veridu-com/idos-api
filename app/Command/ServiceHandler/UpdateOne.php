<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\ServiceHandler;

use App\Command\AbstractCommand;

/**
 * ServiceHandler "Update one" Command.
 */
class UpdateOne extends AbstractCommand {
    /**
     * ServiceHandler's id.
     *
     * @var int
     */
    public $serviceHandlerId;
    /**
     * ServiceHandler's company's id.
     *
     * @var int
     */
    public $companyId;
    /**
     * ServiceHandler's listens attribute.
     *
     * @var string
     */
    public $listens;
    /**
     * Actor.
     *
     * @var \App\Entity\Identity
     */
    public $actor;

    /**
     * {@inheritdoc}
     *
     * @return \App\Command\ServiceHandler\UpdateOne
     */
    public function setParameters(array $parameters) : self {
        if (isset($parameters['listens'])) {
            $this->listens = $parameters['listens'];
        }

        return $this;
    }
}
