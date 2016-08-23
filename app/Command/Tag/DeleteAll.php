<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types=1);

namespace App\Command\Tag;

use App\Command\AbstractCommand;

/**
 * Tag "Delete All" Command.
 */
class DeleteAll extends AbstractCommand {
    /**
     * Tag's targetUser.
     *
     * @var App\Entity\User
     */
    public $targetUser;

    /**
     * {@inheritdoc}
     */
    public function setParameters(array $parameters) : self {
        if (isset($parameters['targetUser'])) {
            $this->targetUser = $parameters['targetUser'];
        }

        return $this;
    }
}
