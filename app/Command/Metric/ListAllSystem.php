<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\Metric;

use App\Command\AbstractCommand;
use App\Command\CommandInterface;

/**
 * Metric ListAllSystem Command.
 */
class ListAllSystem extends AbstractCommand {
    /**
     * Query parameters.
     *
     * @var array
     */
    public $queryParams;
    /**
     * Target company.
     *
     * @var \App\Entity\Company
     */
    public $targetCompany;

    /**
     * {@inheritdoc}
     */
    public function setParameters(array $parameters) : CommandInterface {
        if (isset($parameters['queryParams'])) {
            $this->queryParams = $parameters['queryParams'];
        }

        return $this;
    }
}
