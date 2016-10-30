<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\Profile\Feature;

use App\Command\AbstractCommand;

/**
 * Feature "Delete One" Command.
 */
class DeleteOne extends AbstractCommand {
    /**
     * Feature's User.
     *
     * @var \App\Entity\User
     */
    public $user;
    /**
     * Feature's Service (creator).
     *
     * @var \App\Entity\Service
     */
    public $service;
    /**
     * Feature's id (user input).
     *
     * @var int
     */
    public $featureId;
    /**
     * Actor.
     *
     * @var \App\Entity\Company\Credential
     */
    public $actor;

    /**
     * {@inheritdoc}
     *
     * @return \App\Command\Profile\Feature\DeleteOne
     */
    public function setParameters(array $parameters) : self {
        if (isset($parameters['user'])) {
            $this->user = $parameters['user'];
        }

        if (isset($parameters['service'])) {
            $this->service = $parameters['service'];
        }

        if (isset($parameters['featureId'])) {
            $this->featureId = $parameters['featureId'];
        }

        return $this;
    }
}
