<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\Company\Invitation;

use App\Command\AbstractCommand;

/**
 * Invitation "Update one" Command.
 */
class UpdateOne extends AbstractCommand {
    /**
     * Invitation id.
     *
     * @var int
     */
    public $invitationId;
    /**
     * Invitation's expiration date.
     *
     * @var string
     */
    public $expires;

    /**
     * Whether the email should be re-sent or not.
     *
     * @var bool
     */
    public $resendEmail;

    /**
     * {@inheritdoc}
     */
    public function setParameters(array $parameters) : self {
        if(isset($parameters['expires'])) {
            $this->expires = $parameters['expires'];
        }

        if(isset($parameters['resend_email'])) {
            $this->resendEmail = $parameters['resend_email'];
        }

        return $this;
    }
}
