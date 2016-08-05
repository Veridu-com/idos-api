<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types=1);

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
     *
     * @return App\Command\Company\DeleteAll
     */
    public function setParameters(array $parameters) : self {
        if (isset($parameters['parentId'])) {
            $this->parentId = $parameters['parentId'];
        }

        return $this;
    }
}
