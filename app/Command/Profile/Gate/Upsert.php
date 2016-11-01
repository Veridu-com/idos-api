<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\Profile\Gate;

use App\Command\AbstractCommand;

/**
 * Gate "Create New" Command.
 */
class Upsert extends AbstractCommand {
    /**
     * Gate's user.
     *
     * @var \App\Entity\User
     */
    public $user;
    /**
     * Gate's creator.
     *
     * @var \App\Entity\Service
     */
    public $service;
    /**
     * Gate's name (user input).
     *
     * @var string
     */
    public $name;
    /**
     * Gate's value (user input).
     *
     * @var bool
     */
    public $pass;
    /**
     * Actor.
     *
     * @var \App\Entity\Company\Credential
     */
    public $actor;

    /**
     * {@inheritdoc}
     *
     * @return \App\Command\Profile\Gate\Upsert
     */
    public function setParameters(array $parameters) : self {
        if (isset($parameters['user'])) {
            $this->user = $parameters['user'];
        }

        if (isset($parameters['service'])) {
            $this->service = $parameters['service'];
        }

        if (isset($parameters['name'])) {
            $this->name = $parameters['name'];
        }

        if (isset($parameters['pass'])) {
            $this->pass = $parameters['pass'];
        }

        return $this;
    }
}
