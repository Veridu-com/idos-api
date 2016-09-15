<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\Company;

use App\Command\AbstractCommand;

/**
 * Company "Update One" Command.
 */
class UpdateOne extends AbstractCommand {
    /**
     * Company's new name.
     *
     * @var string
     */
    public $name;
    /**
     * Target Company.
     *
     * @var \App\Entity\Company
     */
    public $company;

    /**
     * Acting Identity.
     *
     * @var \App\Entity\Identity
     */
    public $identity;

    /**
     * {@inheritdoc}
     *
     * @return App\Command\Company\UpdateOne
     */
    public function setParameters(array $parameters) : self {
        if (isset($parameters['name'])) {
            $this->name = $parameters['name'];
        }

        return $this;
    }
}
