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
     * {@inheritdoc}
     */
    public function setParameters(array $parameters) {

        return $this;
    }
}
