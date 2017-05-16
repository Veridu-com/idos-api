<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\Profile\Task;

use App\Command\AbstractCommand;
use App\Command\CommandInterface;

/**
 * Task "Create New" Command.
 */
class CreateNew extends AbstractCommand {
    /**
     * Task's name.
     *
     * @var string
     */
    public $name;
    /**
     * Task's creator.
     *
     * @var \App\Entity\Handler
     */
    public $handler;
    /**
     * Task's trigger event.
     *
     * @var string
     */
    public $event;
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
     * Task's process Id.
     *
     * @var int
     */
    public $processId;
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
        if (isset($parameters['name'])) {
            $this->name = $parameters['name'];
        }

        if (isset($parameters['event'])) {
            $this->event = $parameters['event'];
        }

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
