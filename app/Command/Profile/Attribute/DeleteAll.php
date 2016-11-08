<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\Profile\Attribute;

use App\Command\AbstractCommand;

/**
 * Attribute "Delete All" Command.
 */
class DeleteAll extends AbstractCommand {
    /**
     * Attribute's user.
     *
     * @var \App\Entity\User
     */
    public $user;
    /**
     * Filter.
     *
     * @var array
     */
    public $queryParams;

    /**
     * {@inheritdoc}
     */
    public function setParameters(array $parameters) : self {
        if (isset($parameters['user'])) {
            $this->user = $parameters['user'];
        }

        return $this;
    }
}
