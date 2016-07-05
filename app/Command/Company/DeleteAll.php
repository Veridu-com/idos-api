<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace App\Command\Company;

use App\Command\AbstractCommand;

/**
 * Company "Delete All" Command.
 */
class DeleteAll extends AbstractCommand {
    /**
     * All child companies to this Parent Id will be deleted.
     *
     * @var int
     */
    public $parentId;

    /**
     * {@inheritdoc}
     */
    public function setParameters(array $parameters) {
        if (isset($parameters['parentId']))
            $this->parentId = $parameters['parentId'];

        return $this;
    }
}
