<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\Company\Hook;

use App\Command\AbstractCommand;

/**
 * Hook "Get One" Command.
 */
class GetOne extends AbstractCommand {
    /**
     * Target Company.
     *
     * @var \App\Entity\Company
     */
    public $company;
    /**
     * Target Credential's public key.
     *
     * @var string
     */
    public $credentialPubKey;
    /**
     * Hook id.
     *
     * @var int
     */
    public $hookId;
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
