<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace App\Command\RoleAccess;

use App\Command\AbstractCommand;

/**
 * RoleAccess "Update One" Command.
 */
class UpdateOne extends AbstractCommand {
    /**
     * RoleAccess's role name
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
     * RoleAccess's access value.
     *
     * @var int
     */
    public $access;
    /**
     * RoleAccess's owner's identity id.
     *
     * @var int
     */
    public $identityId;

    /**
     * {@inheritdoc}
     */
    public function setParameters(array $parameters) {
        // only access is updatable
        if (isset($parameters['access'])) {
            $this->access = $parameters['access'];
        }

        return $this;
    }
}
