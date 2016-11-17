<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\ServiceHandler;

use App\Command\AbstractCommand;

/**
 * ServiceHandler "Delete all" Command.
 */
class DeleteAll extends AbstractCommand {
    /**
     * ServiceHandler company's Id.
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
     *
     * @return \App\Command\ServiceHandler\DeleteAll
     */
    public function setParameters(array $parameters) : self {
        if (isset($parameters['companyId'])) {
            $this->companyId = $parameters['companyId'];
        }

        return $this;
    }
}
