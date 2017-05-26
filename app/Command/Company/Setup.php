<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\Company;

use App\Command\AbstractCommand;
use App\Command\CommandInterface;

/**
 * Company "Setup of a Company" Command.
 */
class Setup extends AbstractCommand {
    /**
     * Company id.
     *
     * @var int
     */
    public $companyId;
    /**
     * Identity creating the company.
     *
     * @var \App\Entity\Identity
     */
    public $identity;

    /**
     * {@inheritdoc}
     */
    public function setParameters(array $parameters) : CommandInterface {
        return $this;
    }
}
