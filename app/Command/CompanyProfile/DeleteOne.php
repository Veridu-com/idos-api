<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\CompanyProfile;

use App\Command\AbstractCommand;

/**
 * CompanyProfile "Delete One" Command.
 */
class DeleteOne extends AbstractCommand {
    /**
     * CompanyProfile Id to be deleted.
     *
     * @var int
     */
    public $userId;

    /**
     * {@inheritdoc}
     *
     * @return App\Command\CompanyProfile\DeleteOne
     */
    public function setParameters(array $parameters) : self {
        if (isset($parameters['userId'])) {
            $this->userId = $parameters['userId'];
        }

        return $this;
    }
}
