<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\Profile\Attribute;

use App\Command\AbstractCommand;
use App\Command\CommandInterface;

/**
 * Attribute "Upsert Bulk" Command.
 */
class UpsertBulk extends AbstractCommand {
    /**
     * Attribute's user.
     *
     * @var \App\Entity\User
     */
    public $user;
    /**
     * Attribute array.
     *
     * @var string
     */
    public $attributes;
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

        return $this;
    }
}
