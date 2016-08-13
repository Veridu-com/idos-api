<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types=1);

namespace App\Command\DaemonHandler;

use App\Command\AbstractCommand;

/**
 * DaemonHandler "Delete one" Command.
 */
class DeleteOne extends AbstractCommand {
    /**
     * DaemonHandler's id.
     *
     * @var string
     */
    public $daemonHandlerId;

    /**
     * {@inheritdoc}
     *
     * @return App\Command\DaemonHandler\DeleteOne
     */
    public function setParameters(array $parameters) : self {

        return $this;
    }
}
