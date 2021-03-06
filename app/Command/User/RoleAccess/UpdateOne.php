<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\User\RoleAccess;

use App\Command\AbstractCommand;
use App\Command\CommandInterface;

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
    public function setParameters(array $parameters) : CommandInterface {
        if (isset($parameters['access'])) {
            $this->access = $parameters['access'];
        }

        return $this;
    }
}
