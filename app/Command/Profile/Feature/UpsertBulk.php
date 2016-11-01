<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\Profile\Feature;

use App\Command\AbstractCommand;

/**
 * Feature "UpsertBulk" Command.
 */
class UpsertBulk extends AbstractCommand {
    /**
     * Feature's User.
     *
     * @var \App\Entity\User
     */
    public $user;
    /**
     * Features (user input).
     *
     * @var array
     */
    public $features;
    /**
     * Feature's Service (creator).
     *
     * @var \App\Entity\Service
     */
    public $service;
    /**
     * Target Credential.
     *
     * @var \App\Entity\Company\Credential
     */
    public $credential;

    /**
     * {@inheritdoc}
     *
     * @return \App\Command\Profile\Feature\UpsertBulk
     */
    public function setParameters(array $parameters) : self {

        return $this;
    }
}
