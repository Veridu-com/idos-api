<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\Profile\Reference;

use App\Command\AbstractCommand;
use App\Command\CommandInterface;

/**
 * Reference "Delete All" Command.
 */
class DeleteAll extends AbstractCommand {
    /**
     * Reference's user.
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
        if (isset($parameters['user'])) {
            $this->user = $parameters['user'];
        }

        return $this;
    }
}
