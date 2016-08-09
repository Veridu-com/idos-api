<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types=1);

namespace App\Command\Member;

use App\Command\AbstractCommand;

/**
 * Member "Delete One" Command.
 */
class DeleteOne extends AbstractCommand {
    /**
     * Member id.
     *
     * @var int
     */
    public $memberId;

    /**
     * {@inheritdoc}
     */
    public function setParameters(array $parameters) : self {
        if (isset($parameters['memberId'])) {
            $this->memberId = $parameters['memberId'];
        }

        return $this;
    }
}
