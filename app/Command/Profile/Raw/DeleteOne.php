<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\Profile\Raw;

use App\Command\AbstractCommand;
use App\Command\CommandInterface;

/**
 * Raw "Delete One" Command.
 */
class DeleteOne extends AbstractCommand {
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
     * New raw collection name.
     *
     * @var string
     */
    public $collection;
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

        if (isset($parameters['source'])) {
            $this->source = $parameters['source'];
        }

        if (isset($parameters['collection'])) {
            $this->collection = $parameters['collection'];
        }

        return $this;
    }
}
