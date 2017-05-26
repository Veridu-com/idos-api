<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\Identity;

use App\Command\AbstractCommand;
use App\Command\CommandInterface;

/**
 * Identity "Create New" Command.
 */
class CreateNew extends AbstractCommand {
    /**
     * Profile Id.
     *
     * @var int
     */
    public $profileId;
    /**
     * Source Name.
     *
     * @var string
     */
    public $sourceName;
    /**
     * Application Key.
     *
     * @var string
     */
    public $appKey;

    /**
     * {@inheritdoc}
     */
    public function setParameters(array $parameters) : CommandInterface {
        if (isset($parameters['profileId'])) {
            $this->profileId = $parameters['profileId'];
        }

        if (isset($parameters['sourceName'])) {
            $this->sourceName = $parameters['sourceName'];
        }

        if (isset($parameters['appKey'])) {
            $this->appKey = $parameters['appKey'];
        }

        return $this;
    }
}
