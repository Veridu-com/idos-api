<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\Feature;

use App\Command\AbstractCommand;

/**
 * Feature "Create New" Command.
 */
class CreateNew extends AbstractCommand {
    /**
     * Feature's name (user input).
     *
     * @var string
     */
    public $name;
    /**
     * Feature's value (user input).
     *
     * @var object
     */
    public $value;
    /**
     * Feature's user Id.
     *
     * @var int
     */
    public $userId;

    /**
     * {@inheritdoc}
     *
     * @return App\Command\Feature\CreateNew
     */
    public function setParameters(array $parameters) : self {
        if (isset($parameters['name'])) {
            $this->name = $parameters['name'];
        }

        if (isset($parameters['value'])) {
            $this->value = $parameters['value'];
        }

        if (isset($parameters['userId'])) {
            $this->userId = $parameters['userId'];
        }

        return $this;
    }
}
