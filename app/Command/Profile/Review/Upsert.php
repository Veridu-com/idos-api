<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\Profile\Review;

use App\Command\AbstractCommand;

/**
 * Review Upsert Command.
 */
class Upsert extends AbstractCommand {
    /**
     * User reviewed.
     *
     * @var \App\Entity\User
     */
    public $user;
    /**
     * Review gate id.
     *
     * @var int
     */
    public $gateId;
    /**
     * Review positive.
     *
     * @var string
     */
    public $positive;
    /**
     * Identity.
     *
     * @var \App\Entity\Identity
     */
    public $identity;

    /**
     * {@inheritdoc}
     */
    public function setParameters(array $parameters) : self {
        if (isset($parameters['user'])) {
            $this->user = $parameters['user'];
        }

        if (isset($parameters['positive'])) {
            $this->positive = $parameters['positive'];
        }

        return $this;
    }
}
