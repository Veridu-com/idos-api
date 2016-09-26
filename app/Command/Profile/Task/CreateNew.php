<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\Profile\Task;

use App\Command\AbstractCommand;

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
     * @var App\Entity\Service
     */
    public $service;
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
     * {@inheritdoc}
     *
     * @return App\Command\Profile\Task\CreateNew
     */
    public function setParameters(array $parameters) : self {
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

        if (isset($parameters['processId'])) {
            $this->processId = $parameters['processId'];
        }

        return $this;
    }
}
