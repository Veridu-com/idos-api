<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\Profile\Process;

use App\Command\AbstractCommand;
use App\Command\CommandInterface;

/**
 * Process "Update One" Command.
 */
class UpdateOne extends AbstractCommand {
    /**
     * Process' id.
     *
     * @var int
     */
    public $id;
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

        return $this;
    }
}
