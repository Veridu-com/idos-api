<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\Gate;

use App\Command\AbstractCommand;

/**
 * Gate "Create New" Command.
 */
class Upsert extends AbstractCommand {
    /**
     * Attribute's user.
     *
     * @var App\Entity\User
     */
    public $user;

    /**
     * Attribute's creator.
     *
     * @var App\Entity\Service
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
     * {@inheritdoc}
     *
     * @return App\Command\Gate\Upsert
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
