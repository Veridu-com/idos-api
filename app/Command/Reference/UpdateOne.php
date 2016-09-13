<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\Reference;

use App\Command\AbstractCommand;

/**
 * Reference "Update One" Command.
 */
class UpdateOne extends AbstractCommand {
    /**
     * Reference's user.
     *
     * @var App\Entity\User
     */
    public $user;
    /**
     * New reference name.
     *
     * @var string
     */
    public $name;
    /**
     * New reference value.
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

        if (isset($parameters['name'])) {
            $this->name = $parameters['name'];
        }

        if (isset($parameters['value'])) {
            $this->value = $parameters['value'];
        }

        return $this;
    }
}
