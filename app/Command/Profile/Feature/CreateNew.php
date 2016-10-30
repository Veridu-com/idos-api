<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\Profile\Feature;

use App\Command\AbstractCommand;

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
     * Feature's Service (creator).
     *
     * @var \App\Entity\Service
     */
    public $service;
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
     * @var object
     */
    public $value;

    /**
     * Target Credential.
     *
     * @var \App\Entity\Company\Credential
     */
    public $actor;

    /**
     * {@inheritdoc}
     *
     * @return \App\Command\Profile\Feature\CreateNew
     */
    public function setParameters(array $parameters) : self {
        if (isset($parameters['user'])) {
            $this->user = $parameters['user'];
        }

        if (isset($parameters['source'])) {
            $this->source = $parameters['source'];
        }

        if (isset($parameters['service'])) {
            $this->service = $parameters['service'];
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
