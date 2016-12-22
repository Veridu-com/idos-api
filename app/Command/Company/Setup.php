<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\Company;

use App\Command\AbstractCommand;

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
     *
     * @return \App\Command\Company\InitialSetup
     */
    public function setParameters(array $parameters) : self {

        return $this;
    }
}
