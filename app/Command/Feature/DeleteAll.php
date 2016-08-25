<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\Feature;

use App\Command\AbstractCommand;

/**
 * Feature "Delete All" Command.
 */
class DeleteAll extends AbstractCommand {
    /**
     * User id that whose features will be deleted.
     *
     * @var int
     */
    public $userId;

    /**
     * {@inheritdoc}
     *
     * @return App\Command\Feature\DeleteAll
     */
    public function setParameters(array $parameters) : self {
        if (isset($parameters['userId'])) {
            $this->userId = $parameters['userId'];
        }

        return $this;
    }
}
