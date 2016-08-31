<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\Normalised;

use App\Command\AbstractCommand;

/**
 * Normalised "Update One" Command.
 */
class UpdateOne extends AbstractCommand {
    /**
     * Normalised's user.
     *
     * @var App\Entity\User
     */
    public $user;
    /**
     * Normalised's Source Id.
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
