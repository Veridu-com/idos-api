<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\Company\Permission;

use App\Command\AbstractCommand;
use App\Command\CommandInterface;

/**
 * Permission "Create New" Command.
 */
class CreateNew extends AbstractCommand {
    /**
     * Permission's route name (user input).
     *
     * @var string
     */
    public $routeName;
    /**
     * Company Id.
     *
     * @var int
     */
    public $companyId;
    /**
     * Identity.
     *
     * @var \App\Entity\Identity
     */
    public $identity;

    /**
     * {@inheritdoc}
     */
    public function setParameters(array $parameters) : CommandInterface {
        if (isset($parameters['routeName'])) {
            $this->routeName = $parameters['routeName'];
        }

        if (isset($parameters['companyId'])) {
            $this->companyId = $parameters['companyId'];
        }

        return $this;
    }
}
