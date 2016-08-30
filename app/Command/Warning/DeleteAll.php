<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\Warning;

use App\Command\AbstractCommand;

/**
 * Warning "Delete All" Command.
 */
class DeleteAll extends AbstractCommand {
    /**
     * User id that whose warnings will be deleted.
     *
     * @var int
     */
    public $userId;

    /**
     * {@inheritdoc}
     *
     * @return App\Command\Warning\DeleteAll
     */
    public function setParameters(array $parameters) : self {
        if (isset($parameters['userId'])) {
            $this->userId = $parameters['userId'];
        }

        return $this;
    }
}
