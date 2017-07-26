<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\Profile\Source;

use App\Command\AbstractCommand;
use App\Command\CommandInterface;

/**
 * Source "Update One" Command.
 */
class UpdateOne extends AbstractCommand {
    /**
     * Source's new tags.
     *
     * @var array
     */
    public $tags;
    /**
     * Source Entity.
     *
     * @var \App\Entity\Profile\Source
     */
    public $source;
    /**
     * IP Address.
     *
     * @var string
     */
    public $ipaddr;
    /**
     * OTP code.
     *
     * @var mixed
     */
    public $otpCode;
    /**
     * Source owner User.
     *
     * @var \App\Entity\User
     */
    public $user;
    /**
     * Credential.
     *
     * @var \App\Entity\Company\Credential
     */
    public $credential;

    /**
     * {@inheritdoc}
     */
    public function setParameters(array $parameters) : CommandInterface {
        if (isset($parameters['tags'])) {
            $this->tags = $parameters['tags'];
        }

        return $this;
    }
}
