<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\Profile\Process;

use App\Command\AbstractCommand;
use App\Command\CommandInterface;

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

        if (isset($parameters['userId'])) {
            $this->userId = $parameters['userId'];
        }

        return $this;
    }
}
