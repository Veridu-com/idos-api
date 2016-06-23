<?php
/**
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace App\Command\Company;

use App\Command\AbstractCommand;

/**
 * Company "Update One" Command.
 */
class UpdateOne extends AbstractCommand {
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
     * {@inheritdoc}
     */
    public function setParameters(array $parameters) {
        if (isset($parameters['newName']))
            $this->newName = $parameters['newName'];

        if (isset($parameters['companyId']))
            $this->companyId = $parameters['companyId'];

        return $this;
    }
}
