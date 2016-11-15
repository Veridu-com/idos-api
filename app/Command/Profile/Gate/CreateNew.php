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
     * @var \App\Entity\Service
     */
    public $service;

    /**
     * Gate's slug (user input).
     *
     * @var string
     */
    public $slug;

    /**
     * Gate's confidence level (user input).
     *
     * @var string
     */
    public $confidenceLevel;

    /**
     * Gate's value (user input).
     *
     * @var bool
     */
    public $pass;

    /**
     * {@inheritdoc}
     *
     * @return \App\Command\Profile\Gate\CreateNew
     */
    public function setParameters(array $parameters) : self {
        if (isset($parameters['user'])) {
            $this->user = $parameters['user'];
        }

        if (isset($parameters['service'])) {
            $this->service = $parameters['service'];
        }

        if (isset($parameters['slug'])) {
            $this->slug = $parameters['slug'];
        }

        if (isset($parameters['confidence_level'])) {
            $this->confidenceLevel = $parameters['confidence_level'];
        }

        if (isset($parameters['pass'])) {
            $this->pass = $parameters['pass'];
        }

        return $this;
    }
}
