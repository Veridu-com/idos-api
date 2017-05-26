<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\Company\Profile;

use App\Command\AbstractCommand;
use App\Command\CommandInterface;

/**
 * Company\Profile "Delete One" Command.
 */
class DeleteOne extends AbstractCommand {
    /**
     * Company\Profile Id to be deleted.
     *
     * @var int
     */
    public $userId;
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
        if (isset($parameters['userId'])) {
            $this->userId = $parameters['userId'];
        }

        return $this;
    }
}
