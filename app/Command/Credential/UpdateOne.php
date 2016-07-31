<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types=1);

namespace App\Command\Credential;

use App\Command\AbstractCommand;

/**
 * Credential "Update One" Command.
 */
class UpdateOne extends AbstractCommand {
    /**
     * Credential's new name.
     *
     * @var string
     */
    public $name;
    /**
     * Credential Id.
     *
     * @var int
     */
    public $credentialId;

    /**
     * {@inheritdoc}
     *
     * @return App\Command\Credential\UpdateOne
     */
    public function setParameters(array $parameters) : self {
        if (isset($parameters['name'])) {
            $this->name = $parameters['name'];
        }

        if (isset($parameters['credentialId'])) {
            $this->credentialId = $parameters['credentialId'];
        }

        return $this;
    }
}
