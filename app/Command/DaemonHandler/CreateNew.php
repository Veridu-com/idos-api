<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types=1);

namespace App\Command\DaemonHandler;

use App\Command\AbstractCommand;

/**
 * DaemonHandler "Create New" Command.
 */
class CreateNew extends AbstractCommand {
    /**
     * DaemonHandler's name.
     *
     * @var string
     */
    public $name;

    /**
     * DaemonHandler's source.
     *
     * @var string
     */
    public $source;

    /**
     * DaemonHandler's step.
     *
     * @var string
     */
    public $step;

    /**
     * DaemonHandler's runLevel.
     *
     * @var string
     */
    public $runLevel;

    /**
     * DaemonHandler's location.
     *
     * @var string
     */
    public $location;

    /**
     * DaemonHandler's authPassword.
     *
     * @var string
     */
    public $authPassword;

    /**
     * DaemonHandler's authUsername.
     *
     * @var string
     */
    public $authUsername;

    /**
     * DaemonHandler's company's Id.
     *
     * @var int
     */
    public $companyId;

    /**
     * DaemonHandler daemon's slug.
     *
     * @var int
     */
    public $daemonSlug;

    /**
     * {@inheritdoc}
     *
     * @return App\Command\DaemonHandler\CreateNew
     */
    public function setParameters(array $parameters) : self {
        if (isset($parameters['name'])) {
            $this->name = $parameters['name'];
        }

        if (isset($parameters['source'])) {
            $this->source = $parameters['source'];
        }

        if (isset($parameters['location'])) {
            $this->location = $parameters['location'];
        }

        if (isset($parameters['companyId'])) {
            $this->companyId = $parameters['companyId'];
        }

        if (isset($parameters['daemon'])) {
            $this->daemonSlug = $parameters['daemon'];
        }

        if (isset($parameters['authPassword'])) {
            $this->authPassword = $parameters['authPassword'];
        }

        if (isset($parameters['authUsername'])) {
            $this->authUsername = $parameters['authUsername'];
        }

        if (isset($parameters['runLevel'])) {
            $this->runLevel = $parameters['runLevel'];
        }

        if (isset($parameters['step'])) {
            $this->step = $parameters['step'];
        }

        return $this;
    }
}
