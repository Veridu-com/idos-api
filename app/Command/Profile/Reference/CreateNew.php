<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\Profile\Reference;

use App\Command\AbstractCommand;

/**
 * Reference "Create New" Command.
 */
class CreateNew extends AbstractCommand {
    /**
     * Reference's user.
     *
     * @var \App\Entity\User
     */
    public $user;
    /**
     * Reference ip address.
     *
     * @var string
     */
    public $ipaddr;
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

        if (isset($parameters['name'])) {
            $this->name = $parameters['name'];
        }

        if (isset($parameters['value'])) {
            $this->value = $parameters['value'];
        }

        if (isset($parameters['ipaddr'])) {
            $this->ipaddr = $parameters['ipaddr'];
        }

        return $this;
    }
}
