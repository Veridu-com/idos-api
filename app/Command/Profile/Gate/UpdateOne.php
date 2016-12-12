<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\Profile\Gate;

use App\Command\AbstractCommand;

/**
 * Gate "Update One" Command.
 */
class UpdateOne extends AbstractCommand {
    /**
     * Gate's user.
     *
     * @var \App\Entity\User
     */
    public $user;
    /**
     * Gate's creator.
     *
     * @var \App\Entity\Service
     */
    public $service;
    /**
     * Gate's slug.
     *
     * @var string
     */
    public $slug;
    /**
     * Gate's confidence level (user input).
     *
     * @var string
     */
    public $confidenceLevel;
    /**
     * Credential.
     *
     * @var \App\Entity\Company\Credential
     */
    public $credential;

    /**
     * {@inheritdoc}
     *
     * @return \App\Command\Profile\Gate\UpdateOne
     */
    public function setParameters(array $parameters) : self {
        if (isset($parameters['user'])) {
            $this->user = $parameters['user'];
        }

        if (isset($parameters['service'])) {
            $this->service = $parameters['service'];
        }

        if (isset($parameters['slug'])) {
            $this->slug = $parameters['slug'];
        }

        if (isset($parameters['confidence_level'])) {
            $this->confidenceLevel = $parameters['confidence_level'];
        }

        return $this;
    }
}
