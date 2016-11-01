<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\Profile\Flag;

use App\Command\AbstractCommand;

/**
 * Flag "Create New" Command.
 */
class CreateNew extends AbstractCommand {
    /**
     * Warning's user.
     *
     * @var \App\Entity\User
     */
    public $user;
    /**
     * Warning's creator.
     *
     * @var \App\Entity\Service
     */
    public $service;
    /**
     * Flag's slug (user input).
     *
     * @var string
     */
    public $slug;
    /**
     * Flag's attribute (user input).
     *
     * @var string
     */
    public $attribute;
    /**
     * Credential.
     *
     * @var \App\Entity\Company\Credential
     */
    public $credential;

    /**
     * {@inheritdoc}
     *
     * @return \App\Command\Profile\Flag\CreateNew
     */
    public function setParameters(array $parameters) : self {
        if (isset($parameters['user'])) {
            $this->user = $parameters['user'];
        }

        if (isset($parameters['service'])) {
            $this->service = $parameters['service'];
        }

        if (isset($parameters['slug'])) {
            $this->slug = $parameters['slug'];
        }

        if (isset($parameters['attribute'])) {
            $this->attribute = $parameters['attribute'];
        }

        return $this;
    }
}
