<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\Sso;

use App\Command\AbstractCommand;

/**
 * Sso "Create New" Command.
 */
class CreateNewLinkedin extends AbstractCommand {
    /**
     * API Version.
     *
     * @var string
     */
    public $apiVersion;
    /**
     * Provider access token.
     *
     * @var string
     */
    public $accessToken;
    /**
     * Credential public key.
     *
     * @var string
     */
    public $credentialPubKey;
    /**
     * Credential public key.
     *
     * @var string
     */
    public $key;
    /**
     * Provider secret.
     *
     * @var string
     */
    public $secret;
    /**
     * User ip address.
     *
     * @var string
     */
    public $ipAddress;

    /**
     * {@inheritdoc}
     *
     * @return App\Command\Sso\CreateNewLinkedin
     */
    public function setParameters(array $parameters) : self {
        if (isset($parameters['apiVersion'])) {
            $this->apiVersion = $parameters['apiVersion'];
        }

        if (isset($parameters['key'])) {
            $this->key = $parameters['key'];
        }

        if (isset($parameters['secret'])) {
            $this->secret = $parameters['secret'];
        }

        if (isset($parameters['ipAddress'])) {
            $this->ipAddress = $parameters['ipAddress'];
        }

        if (isset($parameters['accessToken'])) {
            $this->accessToken = $parameters['accessToken'];
        }

        if (isset($parameters['credentialPubKey'])) {
            $this->credentialPubKey = $parameters['credentialPubKey'];
        }

        return $this;
    }
}
