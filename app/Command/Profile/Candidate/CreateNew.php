<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\Profile\Candidate;

use App\Command\AbstractCommand;

/**
 * Candidate "Create New" Command.
 */
class CreateNew extends AbstractCommand {
    /**
     * Candidate's user.
     *
     * @var \App\Entity\User
     */
    public $user;
    /**
     * Candidate's creator.
     *
     * @var \App\Entity\Service
     */
    public $handler;
    /**
     * Attribute name.
     *
     * @var string
     */
    public $attribute;
    /**
     * Candidate value.
     *
     * @var string
     */
    public $value;
    /**
     * Candidate support.
     *
     * @var float
     */
    public $support;
    /**
     * Credential.
     *
     * @var \App\Entity\Company\Credential
     */
    public $credential;

    /**
     * {@inheritdoc}
     */
    public function setParameters(array $parameters) : self {
        if (isset($parameters['user'])) {
            $this->user = $parameters['user'];
        }

        if (isset($parameters['handler'])) {
            $this->handler = $parameters['handler'];
        }

        if (isset($parameters['attribute'])) {
            $this->attribute = $parameters['attribute'];
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
