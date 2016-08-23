<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types=1);

namespace App\Command\Tag;

use App\Command\AbstractCommand;

/**
 * Tag "Create New" Command.
 */
class CreateNew extends AbstractCommand {
    /**
     * Tag's user.
     *
     * @var App\Entity\User
     */
    public $user;
    /**
     * New tag name.
     *
     * @var string
     */
    public $name;

    /**
     * {@inheritdoc}
     */
    public function setParameters(array $parameters) : self {
        if (isset($parameters['user'])) {
            $this->user = $parameters['user'];
        }

        if(isset($parameters['name'])) {
            $this->name = $parameters['name'];
        }

        return $this;
    }
}
