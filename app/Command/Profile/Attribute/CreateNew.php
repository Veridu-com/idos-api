<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\Profile\Attribute;

use App\Command\AbstractCommand;

/**
 * Attribute "Create New" Command.
 */
class CreateNew extends AbstractCommand {
    /**
     * Attribute's user.
     *
     * @var App\Entity\User
     */
    public $user;

    /**
     * Attribute's creator.
     *
     * @var App\Entity\Service
     */
    public $service;

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
     * Attribute support.
     *
     * @var string
     */
    public $support;

    /**
     * {@inheritdoc}
     */
    public function setParameters(array $parameters) : self {
        if (isset($parameters['user'])) {
            $this->user = $parameters['user'];
        }

        if (isset($parameters['service'])) {
            $this->service = $parameters['service'];
        }

        if (isset($parameters['name'])) {
            $this->name = $parameters['name'];
        }

        if (isset($parameters['value'])) {
            $this->value = $parameters['value'];
        }

        if (isset($parameters['support'])) {
            $this->support = $parameters['support'];
        }

        return $this;
    }
}
