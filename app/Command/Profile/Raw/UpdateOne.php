<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\Profile\Raw;

use App\Command\AbstractCommand;

/**
 * Raw "Update One" Command.
 */
class UpdateOne extends AbstractCommand {
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
     * New raw data.
     *
     * @var string
     */
    public $data;
    /**
     * Credential.
     *
     * @var \App\Entity\Company\Credential
     */
    public $credential;

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

        if (isset($parameters['collection'])) {
            $this->collection = $parameters['collection'];
        }

        if (isset($parameters['data'])) {
            $this->data = $parameters['data'];
        }

        return $this;
    }
}
