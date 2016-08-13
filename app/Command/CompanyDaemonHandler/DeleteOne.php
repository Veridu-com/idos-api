<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types=1);

namespace App\Command\CompanyServiceHandler;

use App\Command\AbstractCommand;

/**
 * CompanyServiceHandler "Delete one" Command.
 */
class DeleteOne extends AbstractCommand {
    /**
     * CompanyServiceHandler's Id.
     *
     * @var int
     */
    public $id;

    /**
     * CompanyServiceHandler company's Id.
     *
     * @var int
     */
    public $companyId;

    /**
     * {@inheritdoc}
     *
     * @return App\Command\CompanyServiceHandler\DeleteOne
     */
    public function setParameters(array $parameters) : self {

        return $this;
    }
}
