<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\Profile\Source;

use App\Command\AbstractCommand;
use App\Entity\User;

/**
 * Source "Delete One" Command.
 */
class DeleteOne extends AbstractCommand {
    /**
     * IP Address.
     *
     * @var string
     */
    public $ipaddr;
    /**
     * Source to be deleted.
     *
     * @var \App\Entity\Profile\Source
     */
    public $source;
    /**
     * Source owner User.
     *
     * @var \App\Entity\User
     */
    public $user;

    /**
     * {@inheritdoc}
     *
     * @return \App\Command\Profile\Source\DeleteOne
     */
    public function setParameters(array $parameters) : self {

        return $this;
    }
}
