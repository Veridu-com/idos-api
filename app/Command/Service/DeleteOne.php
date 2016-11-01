<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\Service;

use App\Command\AbstractCommand;

/**
 * Service "Delete one" Command.
 */
class DeleteOne extends AbstractCommand {
    /**
     * Service's id.
     *
     * @var int
     */
    public $serviceId;
    /**
     * Acting company.
     *
     * @var \App\Entity\Company
     */
    public $company;
    /**
     * Actor.
     *
     * @var \App\Entity\Identity
     */
    public $actor;

    /**
     * {@inheritdoc}
     *
     * @return \App\Command\Service\DeleteOne
     */
    public function setParameters(array $parameters) : self {

        return $this;
    }
}
