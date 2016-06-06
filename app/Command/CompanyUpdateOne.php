<?php
/**
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace App\Command;

/**
 * Company "Update One" Command.
 */
class CompanyUpdateOne extends AbstractCommand {
    /**
     * Company's new name.
     *
     * @var string
     */
    public $newName;
    /**
     * Company Id.
     *
     * @var int
     */
    public $companyId;

    /**
     * {@inheritDoc}
     */
    public function setParameters(array $parameters) {
        if (isset($parameters['newName']))
            $this->newName = $parameters['newName'];

        if (isset($parameters['companyId']))
            $this->companyId = $parameters['companyId'];

        return $this;
    }
}
