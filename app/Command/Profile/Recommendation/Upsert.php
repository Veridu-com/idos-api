<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\Profile\Recommendation;

use App\Command\AbstractCommand;

/**
 * Recommendation "Upsert" Command.
 */
class Upsert extends AbstractCommand {
    /**
     * Recommendation User.
     *
     * @var \App\Entity\User
     */
    public $user;
    /**
     * Recommendation Service (creator).
     *
     * @var \App\Entity\Service
     */
    public $service;
    /**
     * Recommendation's Company.
     *
     * @var \App\Entity\Company
     */
    public $company;
    /**
     * Target credential.
     *
     * @var \App\Entity\Company\Credential
     */
    public $credential;
    /**
     * Recommendation's result (user input).
     *
     * @var bool
     */
    public $result;
    /**
     * Recommendation's reasons (user input).
     *
     * @var array
     */
    public $reasons;

    /**
     * {@inheritdoc}
     *
     * @return \App\Command\Profile\Recommendation\Upsert
     */
    public function setParameters(array $parameters) : self {
        if (isset($parameters['result'])) {
            $this->result = $parameters['result'];
        }

        if (isset($parameters['reasons'])) {
            $this->reasons = $parameters['reasons'];
        }

        return $this;
    }
}
