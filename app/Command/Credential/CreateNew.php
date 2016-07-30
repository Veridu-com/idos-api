<?php

/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\Credential;

use App\Command\AbstractCommand;

/**
 * Credential "Create New" Command.
 */
class CreateNew extends AbstractCommand {
    /**
     * Credential Name.
     *
     * @var string
     */
    public $name;
    /**
     * Production flag.
     *
     * @var bool
     */
    public $production = false;
    /**
     * Company Id that this credential belongs to.
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

        if (isset($parameters['production']))
            $this->production = $parameters['production'];

        if (isset($parameters['companyId']))
            $this->companyId = $parameters['companyId'];

        return $this;
    }
}
