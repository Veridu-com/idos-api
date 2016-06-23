<?php
/**
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
     * Credential's new name.
     *
     * @var string
     */
    public $newName;
    /**
     * Credential Id.
     *
     * @var int
     */
    public $credentialId;

    /**
     * {@inheritDoc}
     */
    public function setParameters(array $parameters) {
        if (isset($parameters['newName']))
            $this->newName = $parameters['newName'];

        if (isset($parameters['credentialId']))
            $this->credentialId = $parameters['credentialId'];

        return $this;
    }
}
