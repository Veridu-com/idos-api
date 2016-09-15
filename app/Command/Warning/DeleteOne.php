<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\Warning;

use App\Command\AbstractCommand;

/**
 * Warning "Delete One" Command.
 */
class DeleteOne extends AbstractCommand {
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
     * Gate slug.
     *
     * @var string
     */
    public $slug;

    /**
     * {@inheritdoc}
     *
     * @return App\Command\Gate\DeleteOne
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

        return $this;
    }
}
