<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\Company\Member;

use App\Command\AbstractCommand;

/**
 * Member "Create New" Command.
 */
class CreateNew extends AbstractCommand {
    /**
     * Member's role.
     *
     * @var string
     */
    public $role;
    /**
     * Target Company.
     *
     * @var string
     */
    public $company;
    /**
     * Ip address.
     *
     * @var string
     */
    public $ipaddr;
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
        if(isset($parameters['role'])) {
            $this->role = $parameters['role'];
        }

        if(isset($parameters['ipaddr'])) {
            $this->ipaddr = $parameters['ipaddr'];
        }

        return $this;
    }
}
