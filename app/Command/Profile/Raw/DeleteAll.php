<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\Profile\Raw;

use App\Command\AbstractCommand;
use App\Command\CommandInterface;

/**
 * Raw "Delete All" Command.
 */
class DeleteAll extends AbstractCommand {
    /**
     * Raw's user.
     *
     * @var \App\Entity\User
     */
    public $user;
    /**
     * Query parameters.
     *
     * @var array
     */
    public $queryParams;

    /**
     * {@inheritdoc}
     */
    public function setParameters(array $parameters) : CommandInterface {
        if (isset($parameters['user'])) {
            $this->user = $parameters['user'];
        }

        return $this;
    }
}
