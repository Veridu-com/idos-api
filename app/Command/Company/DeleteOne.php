<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\Company;

use App\Command\AbstractCommand;

/**
 * Company "Delete One" Command.
 */
class DeleteOne extends AbstractCommand {
    /**
     * Company to be deleted.
     *
     * @var \App\Entity\Company
     */
    public $company;
    /**
     * Identity.
     *
     * @var \App\Entity\Identity
     */
    public $identity;

    /**
     * {@inheritdoc}
     *
     * @return \App\Command\Company\DeleteOne
     */
    public function setParameters(array $parameters) : self {

        return $this;
    }
}
