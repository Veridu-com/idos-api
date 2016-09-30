<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\Company\Subscription;

use App\Command\AbstractCommand;

/**
 * Subscription "Create New" Command.
 */
class CreateNew extends AbstractCommand {

    /**
     * Subscription gate id.
     *
     * @var int
     */
    public $gateId;

    /**
     * Subscription warning id.
     *
     * @var int
     */
    public $warningId;

    /**
     * Acting Identity.
     *
     * @var \App\Entity\Identity
     */
    public $identity;

    /**
     * Acting Credential.
     *
     * @var \App\Entity\Credential
     */
    public $credential;

    /**
     * Subscription's credential's public key.
     *
     * @var string
     */
    public $credentialPubKey;

    /**
     * {@inheritdoc}
     *
     * @return App\Command\Company\Subscription\CreateNew
     */
    public function setParameters(array $parameters) : self {
        if (isset($parameters['decoded_gate_id'])) {
            $this->gateId = $parameters['decoded_gate_id'];
        }

        if (isset($parameters['decoded_warning_id'])) {
            $this->warningId = $parameters['decoded_warning_id'];
        }

        if (isset($parameters['identity'])) {
            $this->identity = $parameters['identity'];
        }

        if (isset($parameters['credential'])) {
            $this->credential = $parameters['credential'];
        }

        return $this;
    }
}
