<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\Company\Member;

use App\Command\AbstractCommand;
use App\Command\CommandInterface;

/**
 * Member "Update One" Command.
 */
class UpdateOne extends AbstractCommand {
    /**
     * Member's role (user input).
     *
     * @var string
     */
    public $role;
    /**
     * Target company.
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
     * Member Id.
     *
     * @var int
     */
    public $memberId;
    /**
     * Acting identity.
     *
     * @var \App\Entity\Identity
     */
    public $identity;

    /**
     * {@inheritdoc}
     */
    public function setParameters(array $parameters) : CommandInterface {
        if (isset($parameters['role'])) {
            $this->role = $parameters['role'];
        }

        return $this;
    }
}
