<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\Service;

use App\Command\AbstractCommand;
use App\Command\CommandInterface;

/**
 * Service "Delete one" Command.
 */
class DeleteOne extends AbstractCommand {
    /**
     * Service's id.
     *
     * @var string
     */
    public $serviceId;
    /**
     * Service company's Id.
     *
     * @var int
     */
    public $company;
    /**
     * Identity.
     *
     * @var \App\Entity\Identity
     */
    public $identity;

    /**
     * {@inheritdoc}
     */
    public function setParameters(array $parameters) : CommandInterface {
        return $this;
    }
}
