<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\Profile\Raw;

use App\Command\AbstractCommand;
use App\Command\CommandInterface;

/**
 * Raw "List All" Command.
 */
class ListAll extends AbstractCommand {
    /**
     * Raw's user.
     *
     * @var \App\Entity\User
     */
    public $user;
    /**
     * Raw's Handler.
     *
     * @var \App\Entity\Handler
     */
    public $handler;
    /**
     * Target Credential.
     *
     * @var \App\Entity\Company\Credential
     */
    public $credential;
    /**
     * Query parameters.
     *
     * @var array
     */
    public $queryParams;

    /**
     * {@inheritdoc}
     */
    public function setParameters(array $parameters) : CommandInterface {
        return $this;
    }
}
