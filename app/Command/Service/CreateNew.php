<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\Service;

use App\Command\AbstractCommand;

/**
 * Service "Create new" Command.
 */
class CreateNew extends AbstractCommand {
    /**
     * Service's company's instance.
     *
     * @var \App\Entity\Company
     */
    public $company;

    /**
     * Service's name.
     *
     * @var string
     */
    public $name;

    /**
     * Service's url.
     *
     * @var string
     */
    public $url;

    /**
     * Service's listens.
     *
     * @var array
     */
    public $listens;

    /**
     * Service's triggers.
     *
     * @var array
     */
    public $triggers;

    /**
     * Service's enabled.
     *
     * @var bool
     */
    public $enabled;

    /**
     * Service's access.
     *
     * @var int
     */
    public $access;

    /**
     * Service's authentication username.
     *
     * @var string
     */
    public $authUsername;

    /**
     * Service's authentication password.
     *
     * @var string
     */
    public $authPassword;

    /**
     * {@inheritdoc}
     *
     * @return \App\Command\Service\CreateNew
     */
    public function setParameters(array $parameters) : self {
        if (isset($parameters['name'])) {
            $this->name = $parameters['name'];
        }

        if (isset($parameters['url'])) {
            $this->url = $parameters['url'];
        }

        if (isset($parameters['listens'])) {
            $this->listens = $parameters['listens'];
        }

        if (isset($parameters['triggers'])) {
            $this->triggers = $parameters['triggers'];
        }

        if (isset($parameters['enabled'])) {
            $this->enabled = $parameters['enabled'];
        }

        if (isset($parameters['access'])) {
            $this->access = $parameters['access'];
        }

        if (isset($parameters['auth_username'])) {
            $this->authUsername = $parameters['auth_username'];
        }

        if (isset($parameters['auth_password'])) {
            $this->authPassword = $parameters['auth_password'];
        }

        return $this;
    }
}
