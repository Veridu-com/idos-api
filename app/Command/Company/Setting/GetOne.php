<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\Company\Setting;

use App\Command\AbstractCommand;

/**
 * Setting "Get One" Command.
 */
class GetOne extends AbstractCommand {
    /**
     * Setting Id.
     *
     * @var int
     */
    public $settingId;
    /**
     * Target Company.
     *
     * @var \App\Entity\Company
     */
    public $company;
    /**
     * Parent access.
     *
     * @var bool
     */
    public $hasParentAccess;
    /**
     * Identity.
     *
     * @var \App\Entity\Identity
     */
    public $identity;

    /**
     * {@inheritdoc}
     *
     * @return \App\Command\Company\Setting\GetOne
     */
    public function setParameters(array $parameters) : self {
        return $this;
    }
}
