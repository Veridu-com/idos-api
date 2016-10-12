<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\Profile\Raw;

use App\Command\AbstractCommand;

/**
 * Raw "Create New" Command.
 */
class CreateNew extends AbstractCommand {
    /**
     * Raw's user.
     *
     * @var \App\Entity\User
     */
    public $user;

    /**
     * Raw's Service.
     *
     * @var \App\Entity\Service
     */
    public $service;

    /**
     * Raw's Source.
     *
     * @var \App\Entity\Profile\Source
     */
    public $source;

    /**
     * Target Credential.
     *
     * @var \App\Entity\Company\Credential
     */
    public $credential;

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
