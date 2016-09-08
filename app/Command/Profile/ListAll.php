<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\Profile;

use App\Command\AbstractCommand;

/**
 * Profile ListAll Command.
 */
class ListAll extends AbstractCommand {
    /**
     * Profile's company's instance.
     *
     * @var App\Entity\Company
     */
    public $company;

    /**
     * {@inheritdoc}
     *
     * @return App\Command\Profile\ListAll
     */
    public function setParameters(array $parameters) : self {
        if (isset($parameters['company'])) {
            $this->company = $parameters['company'];
        }

        return $this;
    }
}
