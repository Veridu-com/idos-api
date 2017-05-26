<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\Profile\Tag;

use App\Command\AbstractCommand;
use App\Command\CommandInterface;

/**
 * Tag "Create New" Command.
 */
class CreateNew extends AbstractCommand {
    /**
     * Tagged user.
     *
     * @var \App\Entity\User
     */
    public $user;
    /**
     * New tag name.
     *
     * @var string
     */
    public $name;
    /**
     * New tag slug.
     *
     * @var string
     */
    public $slug;
    /**
     * Identity.
     *
     * @var \App\Entity\Identity
     */
    public $identity;

    /**
     * {@inheritdoc}
     */
    public function setParameters(array $parameters) : CommandInterface {
        if (isset($parameters['user'])) {
            $this->user = $parameters['user'];
        }

        if (isset($parameters['name'])) {
            $this->name = $parameters['name'];
        }

        if (isset($parameters['slug'])) {
            $this->slug = $parameters['slug'];
        }

        return $this;
    }
}
