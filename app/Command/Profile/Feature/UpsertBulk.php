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
 * Feature "Upsert Bulk" Command.
 */
class UpsertBulk extends AbstractCommand {
    /**
     * Feature's User.
     *
     * @var \App\Entity\User
     */
    public $user;
    /**
     * Features (user input).
     *
     * @var array
     */
    public $features;
    /**
     * Feature's Handler (creator).
     *
     * @var \App\Entity\Handler
     */
    public $handler;
    /**
     * Target Credential.
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

        if (isset($parameters['features'])) {
            $this->features = $parameters['features'];
        }

        if (isset($parameters['handler'])) {
            $this->handler = $parameters['handler'];
        }

        if (isset($parameters['credential'])) {
            $this->credential = $parameters['credential'];
        }

        return $this;
    }
}
