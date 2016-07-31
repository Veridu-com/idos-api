<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types=1);

namespace App\Command\Member;

use App\Command\AbstractCommand;

/**
 * Member "Create New" Command.
 */
class CreateNew extends AbstractCommand {
    /**
     * Member's username (user input).
     *
     * @var string
     */
    public $username;
    /**
     * Member's role.
     *
     * @var string
     */
    public $role;
    /**
     * Company Id.
     *
     * @var int
     */
    public $companyId;

    /**
     * {@inheritdoc}
     */
    public function setParameters(array $parameters) : self {
        if (isset($parameters['username'])) {
            $this->username = $parameters['username'];
        }

        if(isset($parameters['role'])) {
            $this->role = $parameters['role'];
        }

        if (isset($parameters['companyId'])) {
            $this->companyId = $parameters['companyId'];
        }

        return $this;
    }
}
