<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\ServiceHandler;

use App\Command\AbstractCommand;

/**
 * ServiceHandler "Delete one" Command.
 */
class DeleteOne extends AbstractCommand {
    /**
     * ServiceHandler's id.
     *
     * @var string
     */
    public $serviceHandlerId;

    /**
     * ServiceHandler company's Id.
     *
     * @var int
     */
    public $companyId;

    /**
     * {@inheritdoc}
     *
     * @return App\Command\ServiceHandler\DeleteOne
     */
    public function setParameters(array $parameters) : self {

        return $this;
    }
}
