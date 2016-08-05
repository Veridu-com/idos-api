<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\ServiceHandler;

use App\Command\AbstractCommand;

/**
 * ServiceHandler "Update one" Command.
 */
class UpdateOne extends AbstractCommand {
    /**
     * ServiceHandler's name.
     *
     * @var string
     */
    public $name;

    /**
     * ServiceHandler's slug.
     *
     * @var string
     */
    public $slug;

    /**
     * ServiceHandler's source.
     *
     * @var string
     */
    public $source;

    /**
     * ServiceHandler's location.
     *
     * @var string
     */
    public $location;

    /**
     * ServiceHandler's authPassword.
     *
     * @var string
     */
    public $authPassword;

    /**
     * ServiceHandler's authUsername.
     *
     * @var string
     */
    public $authUsername;

    /**
     * ServiceHandler company's Id.
     *
     * @var int
     */
    public $companyId;

    /**
     * ServiceHandler service's slug.
     *
     * @var int
     */
    public $serviceSlug;

    /**
     * {@inheritdoc}
     *
     * @return App\Command\ServiceHandler\UpdateOne
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

        if (isset($parameters['authPassword'])) {
            $this->authPassword = $parameters['authPassword'];
        }

        if (isset($parameters['authUsername'])) {
            $this->authUsername = $parameters['authUsername'];
        }

        return $this;
    }
}
