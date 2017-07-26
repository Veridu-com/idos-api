<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\Service;

use App\Command\AbstractCommand;
use App\Command\CommandInterface;

/**
 * Service "Delete all" Command.
 */
class DeleteAll extends AbstractCommand {
    /**
     * Service company's Id.
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
    public function setParameters(array $parameters) : CommandInterface {
        if (isset($parameters['companyId'])) {
            $this->companyId = $parameters['companyId'];
        }

        return $this;
    }
}
