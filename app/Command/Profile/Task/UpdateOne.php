<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\Profile\Task;

use App\Command\AbstractCommand;

/**
 * Task "Update One" Command.
 */
class UpdateOne extends AbstractCommand {
    /**
     * Target User.
     *
     * @var \App\Entity\User
     */
    public $user;
    /**
     * Task's running flag.
     *
     * @var bool
     */
    public $running;
    /**
     * Task's success flag.
     *
     * @var bool
     */
    public $success;
    /**
     * Task's message.
     *
     * @var string
     */
    public $message;
    /**
     * Task's Id.
     *
     * @var int
     */
    public $id;
    /**
     * Target Credential.
     *
     * @var \App\Entity\Company\Credential
     */
    public $credential;

    /**
     * {@inheritdoc}
     */
    public function setParameters(array $parameters) : self {
        if (isset($parameters['running'])) {
            $this->running = $parameters['running'];
        }

        if (isset($parameters['success'])) {
            $this->success = $parameters['success'];
        }

        if (isset($parameters['message'])) {
            $this->message = $parameters['message'];
        }

        return $this;
    }
}
