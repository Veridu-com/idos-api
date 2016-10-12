<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\Profile\Tag;

use App\Command\AbstractCommand;

/**
 * Tag "Delete All" Command.
 */
class DeleteAll extends AbstractCommand {
    /**
     * Tag's user.
     *
     * @var \App\Entity\User
     */
    public $user;

    /**
     * {@inheritdoc}
     */
    public function setParameters(array $parameters) : self {
        if (isset($parameters['user'])) {
            $this->user = $parameters['user'];
        }

        return $this;
    }
}
