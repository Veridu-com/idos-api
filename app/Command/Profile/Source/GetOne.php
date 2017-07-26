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
 * Source "Get One" Command.
 */
class GetOne extends AbstractCommand {
    /**
     * Source's User.
     *
     * @var \App\Entity\User
     */
    public $user;
    /**
     * Target Credential.
     *
     * @var \App\Entity\Company\Credential
     */
    public $credential;
    /**
     * Source Id.
     *
     * @var int
     */
    public $sourceId;
    /**
     * Flag for including Picture on response data.
     *
     * @var bool
     */
    public $includePicture = false;

    /**
     * {@inheritdoc}
     */
    public function setParameters(array $parameters) : CommandInterface {
        if (isset($parameters['includePicture'])) {
            $this->includePicture = (bool) $parameters['includePicture'];
        }

        return $this;
    }
}
