<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\Review;

use App\Command\AbstractCommand;

/**
 * Review "Update One" Command.
 */
class UpdateOne extends AbstractCommand {
    /**
     * Review's user.
     *
     * @var App\Entity\User
     */
    public $user;
    /**
     * Review's id.
     *
     * @var int
     */
    public $id;
    /**
     * New review positive.
     *
     * @var string
     */
    public $positive;

    /**
     * {@inheritdoc}
     */
    public function setParameters(array $parameters) : self {
        if (isset($parameters['user'])) {
            $this->user = $parameters['user'];
        }

        if (isset($parameters['id'])) {
            $this->id = $parameters['id'];
        }

        if (isset($parameters['positive'])) {
            $this->positive = $parameters['positive'];
        }

        return $this;
    }
}
