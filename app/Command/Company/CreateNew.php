<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\Company;

use App\Command\AbstractCommand;

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
     * @var App\Entity\Identity
     */
    public $identity;

    /**
     * {@inheritdoc}
     *
     * @return App\Command\Company\CreateNew
     */
    public function setParameters(array $parameters) : self {
        if (isset($parameters['name'])) {
            $this->name = $parameters['name'];
        }

        return $this;
    }
}
