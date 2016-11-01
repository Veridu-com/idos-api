<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\Profile\Raw;

use App\Command\AbstractCommand;

/**
 * Raw "Delete All" Command.
 */
class DeleteAll extends AbstractCommand {
    /**
     * Raw's user.
     *
     * @var \App\Entity\User
     */
    public $user;
    /**
     * Raw's Source.
     *
     * @var \App\Entity\Profile\Source
     */
    public $source;
    /**
     * Actor.
     *
     * @var \App\Entity\Company\Credential
     */
    public $actor;

    /**
     * {@inheritdoc}
     */
    public function setParameters(array $parameters) : self {
        if (isset($parameters['user'])) {
            $this->user = $parameters['user'];
        }

        if (isset($parameters['source'])) {
            $this->source = $parameters['source'];
        }

        return $this;
    }
}
