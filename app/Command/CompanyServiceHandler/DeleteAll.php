<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types=1);

namespace App\Command\CompanyServiceHandler;

use App\Command\AbstractCommand;

/**
 * CompanyServiceHandler "Delete all" Command.
 */
class DeleteAll extends AbstractCommand {
    /**
     * CompanyServiceHandler company's Id.
     *
     * @var int
     */
    public $companyId;

    /**
     * {@inheritdoc}
     *
     * @return App\Command\CompanyServiceHandler\DeleteAll
     */
    public function setParameters(array $parameters) : self {
        
        return $this;
    }
}
