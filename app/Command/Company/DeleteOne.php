<?php

/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types=1);

namespace App\Command\Company;

use App\Command\AbstractCommand;

/**
 * Company "Delete One" Command.
 */
class DeleteOne extends AbstractCommand {
    /**
     * Company Id to be deleted.
     *
     * @var int
     */
    public $companyId;

    /**
     * {@inheritdoc}
     */
    public function setParameters(array $parameters) : self {
        if (isset($parameters['companyId']))
            $this->companyId = $parameters['companyId'];

        return $this;
    }
}
