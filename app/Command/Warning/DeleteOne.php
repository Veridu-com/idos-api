<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\Warning;

use App\Command\AbstractCommand;

/**
 * Warning "Delete One" Command.
 */
class DeleteOne extends AbstractCommand {
    /**
     * Warning slug.
     *
     * @var string
     */
    public $warningSlug;
    /**
     * User Id.
     *
     * @var int
     */
    public $userId;

    /**
     * {@inheritdoc}
     *
     * @return App\Command\Warning\DeleteOne
     */
    public function setParameters(array $parameters) : self {
        if (isset($parameters['userId'])) {
            $this->userId = $parameters['userId'];
        }

        if (isset($parameters['warningSlug'])) {
            $this->warningSlug = $parameters['warningSlug'];
        }

        return $this;
    }
}
