<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\Company\Hook;

use App\Command\AbstractCommand;

/**
 * Hook "Delete One" Command.
 */
class DeleteOne extends AbstractCommand {
    /**
     * Hook id.
     *
     * @var int
     */
    public $hookId;
    /**
     * Credential public key.
     *
     * @var string
     */
    public $credentialPubKey;
    /**
     * Target Company's id.
     *
     * @var int
     */
    public $companyId;
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
