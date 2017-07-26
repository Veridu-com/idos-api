<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\Company;

use App\Command\AbstractCommand;
use App\Command\CommandInterface;

/**
 * Company "Create New" Command.
 */
class CreateNew extends AbstractCommand {
    /**
     * Company Name.
     *
     * @var string
     */
    public $name;
    /**
     * Company's Parent Id.
     *
     * @var int
     */
    public $parentId;
    /**
     * Identity creating the company.
     *
     * @var \App\Entity\Identity
     */
    public $identity;

    /**
     * {@inheritdoc}
     */
    public function setParameters(array $parameters) : CommandInterface {
        if (isset($parameters['name'])) {
            $this->name = $parameters['name'];
        }

        return $this;
    }
}
