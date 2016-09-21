<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\Company\Member;

use App\Command\AbstractCommand;

/**
 * Member "Delete All" Command.
 */
class DeleteAll extends AbstractCommand {
    /**
     * Id of the company whose members will be deleted.
     *
     * @var string
     */
    public $companyId;
    /**
     * {@inheritdoc}
     */
    public function setParameters(array $parameters) : self {
        if (isset($parameters['companyId'])) {
            $this->companyId = $parameters['companyId'];
        }

        return $this;
    }
}
