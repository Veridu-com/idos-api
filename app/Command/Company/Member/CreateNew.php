<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\Company\Member;

use App\Command\AbstractCommand;

/**
 * Member "Create New" Command.
 */
class CreateNew extends AbstractCommand {
    /**
     * Member's role.
     *
     * @var string
     */
    public $role;
    /**
     * Member's username (user input).
     *
     * @var string
     */
    public $userName;
    /**
     * Credential public key.
     *
     * @var string
     */
    public $credential;

    /**
     * {@inheritdoc}
     */
    public function setParameters(array $parameters) : self {
        if (isset($parameters['userName'])) {
            $this->userName = $parameters['userName'];
        }

        if(isset($parameters['role'])) {
            $this->role = $parameters['role'];
        }

        if(isset($parameters['credential'])) {
            $this->credential = $parameters['credential'];
        }

        return $this;
    }
}
