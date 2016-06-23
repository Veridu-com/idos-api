<?php
/**
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

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
     * {@inheritDoc}
     */
    public function setParameters(array $parameters) {
        if (isset($parameters['name']))
            $this->name = $parameters['name'];

        if (isset($parameters['parentId']))
            $this->parentId = $parameters['parentId'];

        return $this;
    }
}
