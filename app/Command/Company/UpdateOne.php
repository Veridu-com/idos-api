<?php

/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

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
    public $name;
    /**
     * Company Id.
     *
     * @var int
     */
    public $companyId;

    /**
     * {@inheritdoc}
     */
    public function setParameters(array $parameters) : self {
        if (isset($parameters['name']))
            $this->name = $parameters['name'];

        if (isset($parameters['companyId']))
            $this->companyId = $parameters['companyId'];

        return $this;
    }
}
