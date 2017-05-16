<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\Company\Invitation;

use App\Command\AbstractCommand;
use App\Command\CommandInterface;

/**
 * Invitation "Create New" Command.
 */
class CreateNew extends AbstractCommand {
    /**
     * Member's role.
     *
     * @var string
     */
    public $role;
    /**
     * Member's email.
     *
     * @var string
     */
    public $email;
    /**
     * Member's name.
     *
     * @var string
     */
    public $name;
    /**
     * Invitation's expiration date.
     *
     * @var string
     */
    public $expires;
    /**
     * Invitation's related credential public key.
     * Dashboard owner's credential public key.
     *
     * @var string
     */
    public $credentialPubKey;
    /**
     * Invitation's related company.
     * Target company that the member will have access to.
     *
     * @var \App\Entity\Company
     */
    public $company;
    /**
     * Ip address.
     *
     * @var string
     */
    public $ipaddr;
    /**
     * Invitation's related identity, creator of the invitation.
     *
     * @var \App\Entity\Identity
     */
    public $identity;

    /**
     * {@inheritdoc}
     */
    public function setParameters(array $parameters) : CommandInterface {
        if (isset($parameters['email'])) {
            $this->email = $parameters['email'];
        }

        if (isset($parameters['name'])) {
            $this->name = $parameters['name'];
        }

        if (isset($parameters['role'])) {
            $this->role = $parameters['role'];
        }

        if (isset($parameters['credential_public'])) {
            $this->credentialPubKey = $parameters['credential_public'];
        }

        if (isset($parameters['expires'])) {
            $this->expires = $parameters['expires'];
        }

        if (isset($parameters['ipaddr'])) {
            $this->ipaddr = $parameters['ipaddr'];
        }

        return $this;
    }
}
