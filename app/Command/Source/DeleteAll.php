<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\Source;

use App\Command\AbstractCommand;
use App\Entity\User;

/**
 * Source "Delete All" Command.
 */
class DeleteAll extends AbstractCommand {
    /**
     * All sources of this User will be deleted.
     *
     * @var App\Entity\User
     */
    public $user;
    /**
     * IP Address.
     *
     * @var string
     */
    public $ipaddr;

    /**
     * {@inheritdoc}
     *
     * @return App\Command\Source\DeleteAll
     */
    public function setParameters(array $parameters) : self {

        return $this;
    }
}