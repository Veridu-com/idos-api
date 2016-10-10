<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\Profile\Candidate;

use App\Command\AbstractCommand;

/**
 * Candidate "Delete All" Command.
 */
class DeleteAll extends AbstractCommand {
    /**
     * Candidate's user.
     *
     * @var App\Entity\User
     */
    public $user;

    /**
     * Candidate's service.
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
