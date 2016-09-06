<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\Source;

use App\Command\AbstractCommand;
use App\Entity\User;

/**
 * Source "Create New" Command.
 */
class CreateNew extends AbstractCommand {
    /**
     * Source Name.
     *
     * @var string
     */
    public $name;

    /**
     * Source Tags.
     *
     * @var array
     */
    public $tags;

    /**
     * IP Address.
     *
     * @var string
     */
    public $ipaddr;

    /**
     * Source's User.
     *
     * @var App\Entity\User
     */
    public $user;

    /**
     * {@inheritdoc}
     *
     * @return App\Command\Source\CreateNew
     */
    public function setParameters(array $parameters) : self {
        if (isset($parameters['name'])) {
            $this->name = $parameters['name'];
        }

        if (isset($parameters['tags'])) {
            $this->tags = $parameters['tags'];
        }

        if (isset($parameters['user'])) {
            $this->user = $parameters['user'];
        }

        return $this;
    }
}
