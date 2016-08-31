<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\Process;

use App\Command\AbstractCommand;

/**
 * Process "Update One" Command.
 */
class UpdateOne extends AbstractCommand {
    /**
     * Process's id.
     *
     * @var int
     */
    public $id;
    /**
     * User's id.
     *
     * @var int
     */
    public $userId;
    /**
     * Process's name.
     *
     * @var string
     */
    public $name;
    /**
     * Process's trigger event.
     *
     * @var object
     */
    public $event;

    /**
     * {@inheritdoc}
     *
     * @return App\Command\Process\UpdateOne
     */
    public function setParameters(array $parameters) : self {
        if (isset($parameters['id'])) {
            $this->id = $parameters['id'];
        }

        if (isset($parameters['userId'])) {
            $this->userId = $parameters['userId'];
        }

        if (isset($parameters['name'])) {
            $this->name = $parameters['name'];
        }

        if (isset($parameters['event'])) {
            $this->event = $parameters['event'];
        }

        return $this;
    }
}
