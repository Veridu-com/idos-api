<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\Warning;

use App\Command\AbstractCommand;

/**
 * Warning "Create New" Command.
 */
class CreateNew extends AbstractCommand {
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
     * Warning's name (user input).
     *
     * @var string
     */
    public $name;
    /**
     * Warning's reference (user input).
     *
     * @var string
     */
    public $reference;

    /**
     * {@inheritdoc}
     *
     * @return App\Command\Warning\CreateNew
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

        if (isset($parameters['reference'])) {
            $this->reference = $parameters['reference'];
        }

        return $this;
    }
}
