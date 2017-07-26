<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\Profile\Score;

use App\Command\AbstractCommand;
use App\Command\CommandInterface;

/**
 * Score "Delete One" Command.
 */
class DeleteOne extends AbstractCommand {
    /**
     * Score's user.
     *
     * @var \App\Entity\User
     */
    public $user;
    /**
     * Score's Creator.
     *
     * @var \App\Entity\Handler
     */
    public $handler;
    /**
     * New score name.
     *
     * @var string
     */
    public $name;
    /**
     * Credential.
     *
     * @var \App\Entity\Company\Credential
     */
    public $credential;

    /**
     * {@inheritdoc}
     */
    public function setParameters(array $parameters) : CommandInterface {
        if (isset($parameters['user'])) {
            $this->user = $parameters['user'];
        }

        if (isset($parameters['handler'])) {
            $this->handler = $parameters['handler'];
        }

        if (isset($parameters['name'])) {
            $this->name = $parameters['name'];
        }

        return $this;
    }
}
