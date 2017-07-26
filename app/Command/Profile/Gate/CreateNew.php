<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\Profile\Gate;

use App\Command\AbstractCommand;
use App\Command\CommandInterface;

/**
 * Gate "Create New" Command.
 */
class CreateNew extends AbstractCommand {
    /**
     * Gate's user.
     *
     * @var \App\Entity\User
     */
    public $user;
    /**
     * Gate's creator.
     *
     * @var \App\Entity\Handler
     */
    public $handler;
    /**
     * Gate's name (user input).
     *
     * @var string
     */
    public $name;
    /**
     * Gate's confidence level (user input).
     *
     * @var string
     */
    public $confidenceLevel;
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

        if (isset($parameters['name'])) {
            $this->name = $parameters['name'];
        }

        if (isset($parameters['confidence_level'])) {
            $this->confidenceLevel = $parameters['confidence_level'];
        }

        return $this;
    }
}
