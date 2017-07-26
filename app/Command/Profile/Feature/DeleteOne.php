<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\Profile\Feature;

use App\Command\AbstractCommand;
use App\Command\CommandInterface;

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
     * Feature's Handler (creator).
     *
     * @var \App\Entity\Handler
     */
    public $handler;
    /**
     * Feature's id (user input).
     *
     * @var int
     */
    public $featureId;
    /**
     * Credential.
     *
     * @var \App\Entity\Company\Credential
     */
    public $credential;

    /**
     * {@inheritdoc}
     */
    public function setParameters(array $parameters) : CommandInterface {
        if (isset($parameters['user'])) {
            $this->user = $parameters['user'];
        }

        if (isset($parameters['handler'])) {
            $this->handler = $parameters['handler'];
        }

        if (isset($parameters['featureId'])) {
            $this->featureId = $parameters['featureId'];
        }

        return $this;
    }
}
