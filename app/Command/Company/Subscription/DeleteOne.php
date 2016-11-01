<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\Company\Subscription;

use App\Command\AbstractCommand;

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
     * Acting Identity.
     *
     * @var \App\Entity\Identity
     */
    public $actor;

    /**
     * {@inheritdoc}
     *
     * @return \App\Command\Company\Subscription\DeleteOne
     */
    public function setParameters(array $parameters) : self {
        if (isset($parameters['subscriptionId'])) {
            $this->subscriptionId = $parameters['subscriptionId'];
        }

        return $this;
    }
}
