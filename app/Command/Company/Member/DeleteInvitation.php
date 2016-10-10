<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\Company\Member;

use App\Command\AbstractCommand;

/**
 * Member "Delete Invitation" Command.
 */
class DeleteInvitation extends AbstractCommand {
    /**
     * Invitation id.
     *
     * @var int
     */
    public $invitationId;

    /**
     * {@inheritdoc}
     */
    public function setParameters(array $parameters) : self {

        return $this;
    }
}
