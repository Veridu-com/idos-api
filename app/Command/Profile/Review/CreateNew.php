<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\Profile\Review;

use App\Command\AbstractCommand;

/**
 * Review "Create New" Command.
 */
class CreateNew extends AbstractCommand {
    /**
     * User reviewed.
     *
     * @var \App\Entity\User
     */
    public $user;
    /**
     * Review's flagId.
     *
     * @var int
     */
    public $gateId;
    /**
     * New review positive.
     *
     * @var bool
     */
    public $positive;
    /**
     * Reviewer.
     *
     * @var \App\Entity\Identity
     */
    public $actor;

    /**
     * {@inheritdoc}
     */
    public function setParameters(array $parameters) : self {
        if (isset($parameters['user'])) {
            $this->user = $parameters['user'];
        }

        if (isset($parameters['decoded_gate_id'])) {
            $this->gateId = $parameters['decoded_gate_id'];
        }

        if (isset($parameters['positive'])) {
            $this->positive = $parameters['positive'];
        }

        return $this;
    }
}
