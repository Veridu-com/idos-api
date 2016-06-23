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
class UpdateOne extends AbstractCommand {
    /**
     * Credential's new name.
     *
     * @var string
     */
    public $name;
    /**
     * Company Id.
     *
     * @var int
     */
    public $companyId;
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
        if (isset($parameters['name']))
            $this->name = $parameters['name'];

        if (isset($parameters['companyId']))
            $this->companyId = $parameters['companyId'];

        if (isset($parameters['credentialId']))
            $this->credentialId = $parameters['credentialId'];

        return $this;
    }
}
