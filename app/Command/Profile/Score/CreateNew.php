<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\Profile\Score;

use App\Command\AbstractCommand;
use App\Command\CommandInterface;

/**
 * Score "Create New" Command.
 */
class CreateNew extends AbstractCommand {
    /**
     * Score's user.
     *
     * @var \App\Entity\User
     */
    public $user;
    /**
     * Score's creator.
     *
     * @var \App\Entity\Handler
     */
    public $handler;
    /**
     * Score's Attribute.
     *
     * @var \App\Entity\Profile\Attribute
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
     * Credential.
     *
     * @var \App\Entity\Company\Credential
     */
    public $credential;

    /**
     * {@inheritdoc}
     */
    public function setParameters(array $parameters) : CommandInterface {
        if (isset($parameters['user'])) {
            $this->user = $parameters['user'];
        }

        if (isset($parameters['handler'])) {
            $this->handler = $parameters['handler'];
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
