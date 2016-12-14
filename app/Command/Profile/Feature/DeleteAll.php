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
     * @var \App\Entity\User
     */
    public $user;

    /**
     * Feature's Handler (creator).
     *
     * @var \App\Entity\Handler
     */
    public $handler;

    /**
     * Query parameters (user input).
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
     *
     * @return \App\Command\Profile\Feature\DeleteAll
     */
    public function setParameters(array $parameters) : self {
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
