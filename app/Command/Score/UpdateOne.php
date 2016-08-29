<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\Score;

use App\Entity\Attribute;
use App\Command\AbstractCommand;

/**
 * Score "Update One" Command.
 */
class UpdateOne extends AbstractCommand {
    /**
     * Score's user.
     *
     * @var App\Entity\User
     */
    public $user;
    /**
     * Score's Attribute.
     *
     * @var App\Entity\Attribute
     */
    public $attribute;
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

        if (isset($parameters['attribute'])) {
            $this->attribute = $parameters['attribute'];
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
