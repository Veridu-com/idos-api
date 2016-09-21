<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\Token;

use App\Command\AbstractCommand;

/**
 * Token "Exchange" Command.
 */
class Exchange extends AbstractCommand {
    /**
     * User.
     *
     * @var App\Entity\User
     */
    public $user;
    /**
     * Acting Company.
     *
     * @var App\Entity\Company
     */
    public $actingCompany;
    /**
     * Target Company.
     *
     * @var App\Entity\Company
     */
    public $targetCompany;
    /**
     * Credential.
     *
     * @var App\Entity\Company\Credential
     */
    public $credential;

    /**
     * {@inheritdoc}
     */
    public function setParameters(array $parameters) : self {
        if (isset($parameters['user'])) {
            $this->user = $parameters['user'];
        }

        if (isset($parameters['actingCompany'])) {
            $this->actingCompany = $parameters['actingCompany'];
        }

        if (isset($parameters['targetCompany'])) {
            $this->targetCompany = $parameters['targetCompany'];
        }

        if (isset($parameters['credential'])) {
            $this->credential = $parameters['credential'];
        }

        return $this;
    }
}
