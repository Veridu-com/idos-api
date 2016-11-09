<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\Sso;

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
     *
     * @return self
     */
    public function setParameters(array $parameters) : self {
        parent::setParameters($parameters);

        if (isset($parameters['tokenSecret'])) {
            $this->accessToken = $parameters['tokenSecret'];
        }

        return $this;
    }
}
