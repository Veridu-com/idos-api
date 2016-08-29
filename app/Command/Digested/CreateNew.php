<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\Digested;

use App\Command\AbstractCommand;

/**
 * Digested "Create New" Command.
 */
class CreateNew extends AbstractCommand {
    /**
     * Digested's user.
     *
     * @var App\Entity\User
     */
    public $user;
    /**
     * Digested's Source Id.
     *
     * @var int
     */
    public $sourceId;
    /**
     * New digested name.
     *
     * @var string
     */
    public $name;
    /**
     * New digested value.
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
