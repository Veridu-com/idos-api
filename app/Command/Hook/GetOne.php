<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\Hook;

use App\Command\AbstractCommand;

/**
 * Hook "Get One" Command.
 */
class GetOne extends AbstractCommand {
    /**
     * Target Company's id.
     *
     * @var int
     */
    public $companyId;

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
     * {@inheritdoc}
     */
    public function setParameters(array $parameters) : self {

        return $this;
    }
}