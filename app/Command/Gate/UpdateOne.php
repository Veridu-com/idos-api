<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\Gate;

use App\Command\AbstractCommand;

/**
 * Gate "Update One" Command.
 */
class UpdateOne extends AbstractCommand {
    /**
     * Gate's slug.
     *
     * @var string
     */
    public $gateSlug;
    /**
     * User's id.
     *
     * @var int
     */
    public $userId;
    /**
     * Gate's property pass (user input).
     *
     * @var object
     */
    public $pass;

    /**
     * {@inheritdoc}
     *
     * @return App\Command\Gate\UpdateOne
     */
    public function setParameters(array $parameters) : self {
        if (isset($parameters['pass'])) {
            $this->pass = $parameters['pass'];
        }

        if (isset($parameters['userId'])) {
            $this->userId = $parameters['userId'];
        }

        if (isset($parameters['gateSlug'])) {
            $this->gateSlug = $parameters['gateSlug'];
        }

        return $this;
    }
}
