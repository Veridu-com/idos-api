<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\User;

use App\Command\AbstractCommand;

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
     * User's credential Id.
     *
     * @var int
     */
    public $credentialId;

    /**
     * {@inheritdoc}
     *
     * @return App\Command\User\CreateNew
     */
    public function setParameters(array $parameters) : self {
        if (isset($parameters['username'])) {
            $this->username = $parameters['username'];
        }

        if (isset($parameters['role'])) {
            $this->role = $parameters['role'];
        }

        if (isset($parameters['credentialId'])) {
            $this->credentialId = $parameters['credentialId'];
        }

        return $this;
    }
}
