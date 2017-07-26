<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\Profile\Review;

use App\Command\AbstractCommand;
use App\Command\CommandInterface;

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
     * Review's gateId.
     *
     * @var int
     */
    public $gateId;
    /**
     * Review's recommendationId.
     *
     * @var int
     */
    public $recommendationId;
    /**
     * New review positive.
     *
     * @var bool
     */
    public $positive;
    /**
     * New review description.
     *
     * @var string
     */
    public $description;
    /**
     * Identity.
     *
     * @var \App\Entity\Identity
     */
    public $identity;

    /**
     * {@inheritdoc}
     */
    public function setParameters(array $parameters) : CommandInterface {
        if (isset($parameters['user'])) {
            $this->user = $parameters['user'];
        }

        if (isset($parameters['decoded_gate_id'])) {
            $this->gateId = $parameters['decoded_gate_id'];
        }

        if (isset($parameters['decoded_recommendation_id'])) {
            $this->recommendationId = $parameters['decoded_recommendation_id'];
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
