<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\Metric;

use App\Command\AbstractCommand;

/**
 * Metric ListAllUser Command.
 */
class ListAllUser extends AbstractCommand {
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
     *
     * @return \App\Command\Metric\ListAllUser
     */
    public function setParameters(array $parameters) : self {
        if (isset($parameters['queryParams'])) {
            $this->queryParams = $parameters['queryParams'];
        }

        return $this;
    }
}
