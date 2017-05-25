<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\Profile\Feature;

use App\Command\AbstractCommand;
use App\Command\CommandInterface;

/**
 * Feature "Create New" Command.
 */
class CreateNew extends AbstractCommand {
    /**
     * Feature's User.
     *
     * @var \App\Entity\User
     */
    public $user;
    /**
     * Feature's Source (user input).
     *
     * @var \App\Entity\Profile\Source
     */
    public $source;
    /**
     * Feature's Handler (creator).
     *
     * @var \App\Entity\Handler
     */
    public $handler;
    /**
     * Feature's name (user input).
     *
     * @var string
     */
    public $name;
    /**
     * Feature's type (user input).
     *
     * @var string
     */
    public $type;
    /**
     * Feature's value (user input).
     *
     * @var mixed
     */
    public $value;

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

        if (isset($parameters['source'])) {
            $this->source = $parameters['source'];
        }

        if (isset($parameters['handler'])) {
            $this->handler = $parameters['handler'];
        }

        if (isset($parameters['name'])) {
            $this->name = $parameters['name'];
        }

        if (isset($parameters['type'])) {
            $this->type = $parameters['type'];
        }

        if (isset($parameters['value'])) {
            $this->value = $parameters['value'];
        }

        return $this;
    }
}
