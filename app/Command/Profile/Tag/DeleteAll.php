<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\Profile\Tag;

use App\Command\AbstractCommand;
use App\Command\CommandInterface;

/**
 * Tag "Delete All" Command.
 */
class DeleteAll extends AbstractCommand {
    /**
     * Tag's user.
     *
     * @var \App\Entity\User
     */
    public $user;
    /**
     * Identity.
     *
     * @var \App\Entity\Identity
     */
    public $identity;

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
