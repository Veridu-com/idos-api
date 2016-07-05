<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace App\Command\Credential;

use App\Command\AbstractCommand;

/**
 * Credential "Delete One" Command.
 */
class DeleteOne extends AbstractCommand {
    /**
     * Credential Id.
     *
     * @var int
     */
    public $credentialId;

    /**
     * {@inheritdoc}
     */
    public function setParameters(array $parameters) {
        if (isset($parameters['credentialId']))
            $this->credentialId = $parameters['credentialId'];

        return $this;
    }
}
