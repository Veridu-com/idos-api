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
     * RoleAccess's section name identifier (comes from URI).
     *
     * @var string
     */
    public $role;

    /**
     * RoleAccess's resource identifier (comes from URI).
     *
     * @var object
     */
    public $resource;

    /**
     * RoleAccess's owner's identity id.
     *
     * @var object
     */
    public $identityId;

    /**
     * {@inheritdoc}
     */
    public function setParameters(array $parameters) {
        if (isset($parameters['role']))
            $this->role = $parameters['role'];

        if (isset($parameters['resource']))
            $this->resource = $parameters['resource'];

        if (isset($parameters['identityId']))
            $this->identityId = $parameters['identityId'];

        return $this;
    }
}
