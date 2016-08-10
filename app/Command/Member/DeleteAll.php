<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types=1);

namespace App\Command\Member;

use App\Command\AbstractCommand;

/**
 * Member "Delete All" Command.
 */
class DeleteAll extends AbstractCommand {
    /**
     * Credential public key of members to be deleted.
     *
     * @var string
     */
    public $credential;
    /**
     * {@inheritdoc}
     */
    public function setParameters(array $parameters) : self {
        if (isset($parameters['credential'])) {
            $this->credential = $parameters['credential'];
        }

        return $this;
    }
}
