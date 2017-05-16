<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\Company\Widget;

use App\Command\AbstractCommand;
use App\Command\CommandInterface;

/**
 * Widget "Update one" Command.
 */
class UpdateOne extends AbstractCommand {
    /**
     * Widget label.
     *
     * @var string
     */
    public $label;
    /**
     * Widget hash.
     *
     * @var string
     */
    public $hash;
    /**
     * Widget type.
     *
     * @var string
     */
    public $type;
    /**
     * Widget config.
     *
     * @var string
     */
    public $config;
    /**
     * Widget enabled.
     *
     * @var bool
     */
    public $enabled;

    /**
     * Widget creator.
     *
     * @var \App\Entity\Identity
     */
    public $identity;

    /**
     * {@inheritdoc}
     */
    public function setParameters(array $parameters) : CommandInterface {
        if (isset($parameters['label'])) {
            $this->label = $parameters['label'];
        }

        if (isset($parameters['config'])) {
            $this->config = $parameters['config'];
        }

        if (isset($parameters['type'])) {
            $this->type = $parameters['type'];
        }

        if (isset($parameters['enabled'])) {
            $this->enabled = $parameters['enabled'];
        }

        return $this;
    }
}
