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
 * Source "Update One" Command.
 */
class UpdateOne extends AbstractCommand {
    /**
     * Source's new tags.
     *
     * @var array
     */
    public $tags;
    /**
     * Source Id.
     *
     * @var int
     */
    public $sourceId;
    /**
     * Source owner User.
     *
     * @var App\Entity\User
     */
    public $user;

    /**
     * {@inheritdoc}
     *
     * @return App\Command\Source\UpdateOne
     */
    public function setParameters(array $parameters) : self {
        if (isset($parameters['tags'])) {
            $this->tags = $parameters['tags'];
        }

        if (isset($parameters['sourceId'])) {
            $this->sourceId = $parameters['sourceId'];
        }

        if (isset($parameters['user'])) {
            $this->user = $parameters['user'];
        }

        return $this;
    }
}
