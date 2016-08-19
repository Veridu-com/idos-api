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
     * RoleAccess's id.
     *
     * @var string
     */
    public $roleAccessId;

    /**
     * RoleAccess's owner's identity id.
     *
     * @var string
     */
    public $identityId;

    /**
     * RoleAccess's access.
     *
     * @var string
     */
    public $access;

    /**
     * {@inheritdoc}
     */
    public function setParameters(array $parameters) {
        if (isset($parameters['access'])) {
            $this->access = $parameters['access'];
        }

        return $this;
    }
}
