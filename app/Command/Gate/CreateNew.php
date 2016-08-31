<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\Gate;

use App\Command\AbstractCommand;

/**
 * Gate "Create New" Command.
 */
class CreateNew extends AbstractCommand {
    /**
     * Gate's name (user input).
     *
     * @var string
     */
    public $name;
    /**
     * Gate's value (user input).
     *
     * @var bool
     */
    public $pass;
    /**
     * Gate's user Id.
     *
     * @var int
     */
    public $userId;

    /**
     * {@inheritdoc}
     *
     * @return App\Command\Gate\CreateNew
     */
    public function setParameters(array $parameters) : self {
        if (isset($parameters['name'])) {
            $this->name = $parameters['name'];
        }

        if (isset($parameters['pass'])) {
            $this->pass = $parameters['pass'];
        }

        if (isset($parameters['userId'])) {
            $this->userId = $parameters['userId'];
        }

        return $this;
    }
}
