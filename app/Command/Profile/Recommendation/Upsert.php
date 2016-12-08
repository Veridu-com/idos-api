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
     * @var string
     */
    public $result;
    /**
     * Rules that the profile passed (user input).
     *
     * @var array
     */
    public $passed;
    /**
     * Rules that the profile failed to pass (user input).
     *
     * @var array
     */
    public $failed;

    /**
     * {@inheritdoc}
     *
     * @return \App\Command\Profile\Recommendation\Upsert
     */
    public function setParameters(array $parameters) : self {
        if (isset($parameters['result'])) {
            $this->result = $parameters['result'];
        }

        if (isset($parameters['passed'])) {
            $this->passed = $parameters['passed'];
        }

        if (isset($parameters['failed'])) {
            $this->failed = $parameters['failed'];
        }

        return $this;
    }
}
