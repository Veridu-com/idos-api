<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\Profile\Score;

use App\Command\AbstractCommand;
use App\Entity\Profile\Attribute;

/**
 * Score "Update One" Command.
 */
class UpdateOne extends AbstractCommand {
    /**
     * Score's user.
     *
     * @var \App\Entity\User
     */
    public $user;
    /**
     * Score's creator.
     *
     * @var \App\Entity\Service
     */
    public $service;
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
     * Actor.
     *
     * @var \App\Entity\Company\Credential
     */
    public $actor;

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
