<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\Profile\Flag;

use App\Command\AbstractCommand;
use App\Command\CommandInterface;

/**
 * Flag "Delete One" Command.
 */
class DeleteOne extends AbstractCommand {
    /**
     * Flag's user.
     *
     * @var \App\Entity\User
     */
    public $user;
    /**
     * Flag's creator.
     *
     * @var \App\Entity\Handler
     */
    public $handler;
    /**
     * Flag slug.
     *
     * @var string
     */
    public $slug;
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

        if (isset($parameters['slug'])) {
            $this->slug = $parameters['slug'];
        }

        return $this;
    }
}
