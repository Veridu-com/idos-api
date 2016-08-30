<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\Mapped;

use App\Command\AbstractCommand;

/**
 * Mapped "Update One" Command.
 */
class UpdateOne extends AbstractCommand {
    /**
     * Mapped's user.
     *
     * @var App\Entity\User
     */
    public $user;
    /**
     * Mapped's Source Id.
     *
     * @var int
     */
    public $sourceId;
    /**
     * New mapped name.
     *
     * @var string
     */
    public $name;
    /**
     * New mapped value.
     *
     * @var string
     */
    public $value;

    /**
     * {@inheritdoc}
     */
    public function setParameters(array $parameters) : self {
        if (isset($parameters['user'])) {
            $this->user = $parameters['user'];
        }

        if (isset($parameters['sourceId'])) {
            $this->sourceId = $parameters['sourceId'];
        }

        if (isset($parameters['name'])) {
            $this->name = $parameters['name'];
        }

        if (isset($parameters['value'])) {
            $this->value = $parameters['value'];
        }

        return $this;
    }
}
