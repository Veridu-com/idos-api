<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\Process;

use App\Command\AbstractCommand;

/**
 * Process "Create New" Command.
 */
class CreateNew extends AbstractCommand {
    /**
     * Process's name.
     *
     * @var string
     */
    public $name;
    /**
     * Process's trigger event.
     *
     * @var string
     */
    public $event;
    /**
     * Process's user Id.
     *
     * @var int
     */
    public $userId;

    /**
     * {@inheritdoc}
     *
     * @return App\Command\Process\CreateNew
     */
    public function setParameters(array $parameters) : self {
        if (isset($parameters['name'])) {
            $this->name = $parameters['name'];
        }

        if (isset($parameters['event'])) {
            $this->event = $parameters['event'];
        }

        if (isset($parameters['userId'])) {
            $this->userId = $parameters['userId'];
        }

        return $this;
    }
}
