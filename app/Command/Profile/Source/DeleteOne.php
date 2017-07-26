<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\Profile\Source;

use App\Command\AbstractCommand;
use App\Command\CommandInterface;

/**
 * Source "Delete One" Command.
 */
class DeleteOne extends AbstractCommand {
    /**
     * IP Address.
     *
     * @var string
     */
    public $ipaddr;
    /**
     * Source to be deleted.
     *
     * @var \App\Entity\Profile\Source
     */
    public $source;
    /**
     * Source owner User.
     *
     * @var \App\Entity\User
     */
    public $user;
    /**
     * Credential.
     *
     * @var \App\Entity\Company\Credential
     */
    public $credential;

    /**
     * {@inheritdoc}
     */
    public function setParameters(array $parameters) : CommandInterface {
        return $this;
    }
}
