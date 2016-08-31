<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\Gate;

use App\Command\AbstractCommand;

/**
 * Gate "Delete All" Command.
 */
class DeleteAll extends AbstractCommand {
    /**
     * User id that whose gates will be deleted.
     *
     * @var int
     */
    public $userId;

    /**
     * {@inheritdoc}
     *
     * @return App\Command\Gate\DeleteAll
     */
    public function setParameters(array $parameters) : self {
        if (isset($parameters['userId'])) {
            $this->userId = $parameters['userId'];
        }

        return $this;
    }
}
