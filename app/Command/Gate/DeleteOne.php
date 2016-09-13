<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\Gate;

use App\Command\AbstractCommand;

/**
 * Gate "Delete One" Command.
 */
class DeleteOne extends AbstractCommand {
    /**
     * Gate slug.
     *
     * @var string
     */
    public $gateSlug;
    /**
     * User Id.
     *
     * @var int
     */
    public $userId;

    /**
     * {@inheritdoc}
     *
     * @return App\Command\Gate\DeleteOne
     */
    public function setParameters(array $parameters) : self {
        if (isset($parameters['userId'])) {
            $this->userId = $parameters['userId'];
        }

        if (isset($parameters['gateSlug'])) {
            $this->gateSlug = $parameters['gateSlug'];
        }

        return $this;
    }
}
