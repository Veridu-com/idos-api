<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\Raw;

use App\Command\AbstractCommand;

/**
 * Raw "Delete All" Command.
 */
class DeleteAll extends AbstractCommand {
    /**
     * Raw's user.
     *
     * @var App\Entity\User
     */
    public $user;
    /**
     * Raw's Source.
     *
     * @var App\Entity\Source
     */
    public $source;

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
