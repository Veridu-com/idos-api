<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace App\Command\RoleAccess;

use App\Command\AbstractCommand;

/**
 * RoleAccess "Delete One" Command.
 */
class DeleteOne extends AbstractCommand {
    /**
     * RoleAccess's role
     *
     * @var string
     */
    public $role;

    /**
     * RoleAccess's resource
     *
     * @var string
     */
    public $resource;

    /**
     * RoleAccess's owner's identity id
     *
     * @var string
     */
    public $identityId;

    /**
     * {@inheritdoc}
     */
    public function setParameters(array $parameters) {
        if (isset($parameters['role'])) {
            $this->role = $parameters['role'];
        }

        if (isset($parameters['resource'])) {
            $this->resource = $parameters['resource'];
        }

        if (isset($parameters['identityId'])) {
            $this->identityId = $parameters['identityId'];
        }

        return $this;
    }
}
