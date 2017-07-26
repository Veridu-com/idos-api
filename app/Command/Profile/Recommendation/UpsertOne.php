<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\Profile\Recommendation;

use App\Command\AbstractCommand;
use App\Command\CommandInterface;

/**
 * Recommendation "Upsert One" Command.
 */
class UpsertOne extends AbstractCommand {
    /**
     * Recommendation User.
     *
     * @var \App\Entity\User
     */
    public $user;
    /**
     * Recommendation Handler (creator).
     *
     * @var \App\Entity\Handler
     */
    public $handler;
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
     */
    public function setParameters(array $parameters) : CommandInterface {
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
