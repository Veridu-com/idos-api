<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\HandlerService;

use App\Command\AbstractCommand;

/**
 * HandlerService "Create new" Command.
 */
class CreateNew extends AbstractCommand {
    /**
     * HandlerService's company's instance.
     *
     * @var \App\Entity\Company
     */
    public $company;
    /**
     * HandlerService's name.
     *
     * @var string
     */
    public $name;
    /**
     * HandlerService's url.
     *
     * @var string
     */
    public $url;
    /**
     * HandlerService's privacy.
     *
     * @var int
     */
    public $privacy;
    /**
     * HandlerService's privacy.
     *
     * @var int
     */
    public $handlerId;
    /**
     * HandlerService's listens.
     *
     * @var array
     */
    public $listens;
    /**
     * HandlerService's enabled.
     *
     * @var bool
     */
    public $enabled;
    /**
     * Identity.
     *
     * @var \App\Entity\Identity
     */
    public $identity;

    /**
     * {@inheritdoc}
     *
     * @return \App\Command\HandlerService\CreateNew
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

        if (isset($parameters['enabled'])) {
            $this->enabled = $parameters['enabled'];
        }

        if (isset($parameters['privacy'])) {
            $this->privacy = $parameters['privacy'];
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
