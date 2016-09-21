<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\Profile\Feature;

use App\Command\AbstractCommand;

/**
 * Feature "Delete All" Command.
 */
class DeleteAll extends AbstractCommand {
    /**
     * Feature's User.
     *
     * @var App\Entity\User
     */
    public $user;

    /**
     * Feature's Service (creator).
     *
     * @var App\Entity\Service
     */
    public $service;

    /**
     * Query parameters (user input).
     *
     * @var array
     */
    public $queryParams;

    /**
     * {@inheritdoc}
     *
     * @return App\Command\Profile\Feature\DeleteAll
     */
    public function setParameters(array $parameters) : self {
        if (isset($parameters['user'])) {
            $this->user = $parameters['user'];
        }

        if (isset($parameters['service'])) {
            $this->service = $parameters['service'];
        }

        if (isset($parameters['queryParams'])) {
            $this->queryParams = $parameters['queryParams'];
        }

        return $this;
    }
}
