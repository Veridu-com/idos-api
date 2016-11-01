<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\Profile\Reference;

use App\Command\AbstractCommand;

/**
 * Reference "Delete One" Command.
 */
class DeleteOne extends AbstractCommand {
    /**
     * Reference's user.
     *
     * @var \App\Entity\User
     */
    public $user;
    /**
     * New reference name.
     *
     * @var string
     */
    public $name;
    /**
     * Actor.
     *
     * @var \App\Entity\Company\Credential
     */
    public $actor;

    /**
     * {@inheritdoc}
     */
    public function setParameters(array $parameters) : self {
        if (isset($parameters['user'])) {
            $this->user = $parameters['user'];
        }

        if (isset($parameters['name'])) {
            $this->name = $parameters['name'];
        }

        return $this;
    }
}
