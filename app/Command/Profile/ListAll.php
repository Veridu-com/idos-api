<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\Profile;

use App\Command\AbstractCommand;
use App\Command\CommandInterface;

/**
 * Profile ListAll Command.
 */
class ListAll extends AbstractCommand {
    /**
     * Profile's company's instance.
     *
     * @var \App\Entity\Company
     */
    public $company;
    /**
     * Credential.
     *
     * @var \App\Entity\Company\Credential
     */
    public $credential;

    /**
     * {@inheritdoc}
     */
    public function setParameters(array $parameters) : CommandInterface {
        if (isset($parameters['company'])) {
            $this->company = $parameters['company'];
        }

        return $this;
    }
}
