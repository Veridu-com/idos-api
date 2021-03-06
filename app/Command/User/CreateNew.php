<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\User;

use App\Command\AbstractCommand;
use App\Command\CommandInterface;

/**
 * User "Create New" Command.
 */
class CreateNew extends AbstractCommand {
    /**
     * User's name .
     *
     * @var string
     */
    public $username;
    /**
     * User's role.
     *
     * @var string
     */
    public $role;
    /**
     * User's owner credential.
     *
     * @var \App\Entity\Company\Credential
     */
    public $credential;

    /**
     * {@inheritdoc}
     */
    public function setParameters(array $parameters) : CommandInterface {
        if (isset($parameters['username'])) {
            $this->username = $parameters['username'];
        }

        if (isset($parameters['role'])) {
            $this->role = $parameters['role'];
        }

        if (isset($parameters['credential'])) {
            $this->credential = $parameters['credential'];
        }

        return $this;
    }
}
