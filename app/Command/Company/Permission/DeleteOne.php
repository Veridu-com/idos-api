<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\Company\Permission;

use App\Command\AbstractCommand;

/**
 * Permission "Delete One" Command.
 */
class DeleteOne extends AbstractCommand {
    /**
     * Permission's route name (user input).
     *
     * @var object
     */
    public $routeName;
    /**
     * Company Id.
     *
     * @var int
     */
    public $companyId;

    /**
     * {@inheritdoc}
     *
     * @return App\Command\Company\Permission\DeleteOne
     */
    public function setParameters(array $parameters) : self {
        if (isset($parameters['routeName'])) {
            $this->routeName = $parameters['routeName'];
        }

        if (isset($parameters['companyId'])) {
            $this->companyId = $parameters['companyId'];
        }

        return $this;
    }
}
