<?php

declare(strict_types=1);
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace App\Command\Permission;

use App\Command\AbstractCommand;

/**
 * Permission "Create New" Command.
 */
class CreateNew extends AbstractCommand {
    /**
     * Permission's route name (user input).
     *
     * @var object
     */
    public $routeName;
    /**
     * Permission Id.
     *
     * @var int
     */
    public $companyId;

    /**
     * {@inheritdoc}
     */
    public function setParameters(array $parameters) : self {
        if (isset($parameters['routeName']))
            $this->routeName = $parameters['routeName'];

        if (isset($parameters['companyId']))
            $this->companyId = $parameters['companyId'];

        return $this;
    }
}
