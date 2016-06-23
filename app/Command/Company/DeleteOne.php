<?php
/**
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace App\Command\Company;

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
     * {@inheritDoc}
     */
    public function setParameters(array $parameters) {
        if (isset($parameters['companyId']))
            $this->companyId = $parameters['companyId'];

        return $this;
    }
}
