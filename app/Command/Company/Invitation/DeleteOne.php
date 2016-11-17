<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\Company\Invitation;

use App\Command\AbstractCommand;

/**
 * Invitation "DeleteOne" Command.
 */
class DeleteOne extends AbstractCommand {
    /**
     * Invitation id.
     *
     * @var int
     */
    public $invitationId;
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

        return $this;
    }
}
