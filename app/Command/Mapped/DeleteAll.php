<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types=1);

namespace App\Command\Mapped;

use App\Command\AbstractCommand;

/**
 * Mapped "Delete All" Command.
 */
class DeleteAll extends AbstractCommand {
    /**
     * Mapped's user.
     *
     * @var App\Entity\User
     */
    public $user;
    /**
     * Mapped's Source Id.
     *
     * @var int
     */
    public $sourceId;

    /**
     * {@inheritdoc}
     */
    public function setParameters(array $parameters) : self {
        if (isset($parameters['user'])) {
            $this->user = $parameters['user'];
        }

        if (isset($parameters['sourceId'])) {
            $this->sourceId = $parameters['sourceId'];
        }

        return $this;
    }
}
