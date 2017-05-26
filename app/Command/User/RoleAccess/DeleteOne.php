<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\User\RoleAccess;

use App\Command\AbstractCommand;
use App\Command\CommandInterface;

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
    public function setParameters(array $parameters) : CommandInterface {
        return $this;
    }
}
