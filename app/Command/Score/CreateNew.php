<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\Score;

use App\Command\AbstractCommand;
use App\Entity\Attribute;

/**
 * Score "Create New" Command.
 */
class CreateNew extends AbstractCommand {
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
     * New score name.
     *
     * @var string
     */
    public $name;
    /**
     * New score value.
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