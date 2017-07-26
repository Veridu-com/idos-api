<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\Profile\Candidate;

use App\Command\AbstractCommand;
use App\Command\CommandInterface;

/**
 * Candidate "Delete All" Command.
 */
class DeleteAll extends AbstractCommand {
    /**
     * Candidate's user.
     *
     * @var \App\Entity\User
     */
    public $user;
    /**
     * Candidate's handler.
     *
     * @var \App\Entity\Service
     */
    public $handler;
    /**
     * Query params.
     *
     * @var array
     */
    public $queryParams;
    /**
     * Credential.
     *
     * @var \App\Entity\Company\Credential
     */
    public $credential;

    /**
     * {@inheritdoc}
     */
    public function setParameters(array $parameters) : CommandInterface {
        if (isset($parameters['user'])) {
            $this->user = $parameters['user'];
        }

        if (isset($parameters['handler'])) {
            $this->handler = $parameters['handler'];
        }

        if (isset($parameters['queryParams'])) {
            $this->queryParams = $parameters['queryParams'];
        }

        return $this;
    }
}
