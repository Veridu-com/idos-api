<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\Company\Subscription;

use App\Command\AbstractCommand;
use App\Command\CommandInterface;

/**
 * Subscription "Delete One" Command.
 */
class DeleteOne extends AbstractCommand {
    /**
     * Subscription's id to be deleted.
     *
     * @var int
     */
    public $subscriptionId;
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
        if (isset($parameters['subscriptionId'])) {
            $this->subscriptionId = $parameters['subscriptionId'];
        }

        return $this;
    }
}
