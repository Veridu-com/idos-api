<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types=1);

namespace App\Command\Hook;

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
     * Company.
     *
     * @var App\Entity\Company
     */
    public $company;

    /**
     * {@inheritdoc}
     */
    public function setParameters(array $parameters) : self {
        if (isset($parameters['hookId'])) {
            $this->hookId = $parameters['hookId'];
        }
        if (isset($parameters['credentialPubKey'])) {
            $this->credentialPubKey = $parameters['credentialPubKey'];
        }
        if (isset($parameters['company'])) {
            $this->company = $parameters['company'];
        }

        return $this;
    }
}
