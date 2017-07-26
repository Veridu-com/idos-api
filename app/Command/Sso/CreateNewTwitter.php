<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\Sso;

use App\Command\CommandInterface;

/**
 * Sso "Create New Twitter" Command.
 */
class CreateNewTwitter extends CreateNew {
    /**
     * Provider token secret.
     *
     * @var string
     */
    public $tokenSecret;

    /**
     * {@inheritdoc}
     */
    public function setParameters(array $parameters) : CommandInterface {
        parent::setParameters($parameters);

        if (isset($parameters['tokenSecret'])) {
            $this->accessToken = $parameters['tokenSecret'];
        }

        return $this;
    }
}
