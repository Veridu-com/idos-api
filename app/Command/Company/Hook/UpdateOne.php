<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\Company\Hook;

use App\Command\AbstractCommand;

/**
 * Hook "Update One" Command.
 */
class UpdateOne extends AbstractCommand {
    /**
     * Hook Id.
     *
     * @var int
     */
    public $hookId;
    /**
     * Hook's trigger (user input).
     *
     * @var string
     */
    public $trigger;
    /**
     * Hook's url (user input).
     *
     * @var string
     */
    public $url;
    /**
     * If hook if subscribed (user input).
     *
     * @var bool
     */
    public $subscribed;
    /**
     * Credential public key.
     *
     * @var string
     */
    public $credentialPubKey;
    /**
     * Target Company.
     *
     * @var \App\Entity\Company
     */
    public $company;
    /**
     * Identity.
     *
     * @var \App\Entity\Identity
     */
    public $identity;

    /**
     * {@inheritdoc}
     */
    public function setParameters(array $parameters) : self {
        if (isset($parameters['hookId'])) {
            $this->hookId = $parameters['hookId'];
        }

        if (isset($parameters['trigger'])) {
            $this->trigger = $parameters['trigger'];
        }

        if (isset($parameters['url'])) {
            $this->url = $parameters['url'];
        }

        if (isset($parameters['subscribed'])) {
            $this->subscribed = $parameters['subscribed'];
        }

        if (isset($parameters['credentialPubKey'])) {
            $this->credentialPubKey = $parameters['credentialPubKey'];
        }

        if (isset($parameters['company'])) {
            $this->company = $parameters['company'];
        }

        return $this;
    }
}
