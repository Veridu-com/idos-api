<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\Handler;

use App\Command\AbstractCommand;
use App\Command\CommandInterface;

/**
 * Handler "Update one" Command.
 */
class UpdateOne extends AbstractCommand {
    /**
     * Handler's id.
     *
     * @var int
     */
    public $handlerId;
    /**
     * Handler's company's instance.
     *
     * @var \App\Entity\Company
     */
    public $company;
    /**
     * Handler's name.
     *
     * @var string
     */
    public $name;
    /**
     * Handler's enabled.
     *
     * @var bool
     */
    public $enabled;
    /**
     * Handler's authentication username.
     *
     * @var string
     */
    public $authUsername;
    /**
     * Handler's authentication password.
     *
     * @var string
     */
    public $authPassword;
    /**
     * Identity.
     *
     * @var \App\Entity\Identity
     */
    public $identity;

    /**
     * {@inheritdoc}
     */
    public function setParameters(array $parameters) : CommandInterface {
        if (isset($parameters['name'])) {
            $this->name = $parameters['name'];
        }

        if (isset($parameters['auth_username'])) {
            $this->authUsername = $parameters['auth_username'];
        }

        if (isset($parameters['auth_password'])) {
            $this->authPassword = $parameters['auth_password'];
        }

        if (isset($parameters['enabled'])) {
            $this->enabled = $parameters['enabled'];
        }

        return $this;
    }
}
