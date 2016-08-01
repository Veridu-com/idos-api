<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types=1);

namespace App\Command\Member;

use App\Command\AbstractCommand;

/**
 * Member "Update One" Command.
 */
class UpdateOne extends AbstractCommand {
    /**
     * Member's role (user input).
     *
     * @var string
     */
    public $role;
    /**
     * Company Id.
     *
     * @var int
     */
    public $companyId;
    /**
     * User Id
     *
     * @var int
     */
    public $userId;
    /**
     * {@inheritdoc}
     */
    public function setParameters(array $parameters) : self {
        if (isset($parameters['userId'])) {
            $this->userId = $parameters['userId'];
        }

        if (isset($parameters['role'])) {
            $this->role = $parameters['role'];
        }

        if (isset($parameters['companyId'])) {
            $this->companyId = $parameters['companyId'];
        }

        return $this;
    }
}
