<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\Gate;

use App\Command\AbstractCommand;

/**
 * Gate "Update One" Command.
 */
class UpdateOne extends AbstractCommand {
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
     * Gate's slug.
     *
     * @var string
     */
    public $slug;
    
    /**
     * Gate's property pass (user input).
     *
     * @var object
     */
    public $pass;

    /**
     * {@inheritdoc}
     *
     * @return App\Command\Gate\UpdateOne
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

        if (isset($parameters['pass'])) {
            $this->pass = $parameters['pass'];
        }

        return $this;
    }
}
