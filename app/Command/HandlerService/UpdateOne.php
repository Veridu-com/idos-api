<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\HandlerService;

use App\Command\AbstractCommand;

/**
 * HandlerService "Update one" Command.
 */
class UpdateOne extends AbstractCommand {
    /**
     * HandlerService's id.
     *
     * @var int
     */
    public $handlerId;
    /**
     * HandlerService's company's instance.
     *
     * @var \App\Entity\Company
     */
    public $company;
    /**
     * HandlerService's url.
     *
     * @var string
     */
    public $url;
    /**
     * HandlerService's listens.
     *
     * @var array
     */
    public $listens;
    /**
     * HandlerService's triggers.
     *
     * @var array
     */
    public $triggers;
    /**
     * HandlerService's enabled.
     *
     * @var bool
     */
    public $enabled;
    /**
     * HandlerService's access.
     *
     * @var int
     */
    public $access;
    /**
     * HandlerService's authentication username.
     *
     * @var string
     */
    public $authUsername;
    /**
     * HandlerService's authentication password.
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
     *
     * @return \App\Command\HandlerService\UpdateOne
     */
    public function setParameters(array $parameters) : self {
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
