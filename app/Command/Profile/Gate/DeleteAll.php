<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\Profile\Gate;

use App\Command\AbstractCommand;

/**
 * Gate "Delete All" Command.
 */
class DeleteAll extends AbstractCommand {
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
     * Query params.
     *
     * @var array
     */
    public $queryParams;

    /**
     * {@inheritdoc}
     *
     * @return App\Command\Profile\Gate\DeleteAll
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