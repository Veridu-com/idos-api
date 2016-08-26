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
     * Company Id to be deleted.
     *
     * @var App\Entity\Company
     */
    public $company;

    /**
     * {@inheritdoc}
     *
     * @return App\Command\Company\DeleteOne
     */
    public function setParameters(array $parameters) : self {
        if (isset($parameters['company'])) {
            $this->company = $parameters['company'];
        }

        return $this;
    }
}
