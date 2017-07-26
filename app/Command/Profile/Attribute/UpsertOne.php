<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\Profile\Attribute;

use App\Command\AbstractCommand;
use App\Command\CommandInterface;

/**
 * Attribute "Upsert One" Command.
 */
class UpsertOne extends AbstractCommand {
    /**
     * Attribute's user.
     *
     * @var \App\Entity\User
     */
    public $user;
    /**
     * Attribute name.
     *
     * @var string
     */
    public $name;
    /**
     * Attribute value.
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

        if (isset($parameters['name'])) {
            $this->name = $parameters['name'];
        }

        if (isset($parameters['value'])) {
            $this->value = $parameters['value'];
        }

        return $this;
    }
}
