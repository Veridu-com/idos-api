<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\Metric;

use App\Command\AbstractCommand;
use App\Command\CommandInterface;

/**
 * Metric CreateNew Command.
 */
class CreateNew extends AbstractCommand {
    /**
     * The event that originated the metric.
     *
     * @var \League\Event\EventInterface
     */
    public $event;

    /**
     * {@inheritdoc}
     */
    public function setParameters(array $parameters) : CommandInterface {
        if (isset($parameters['event'])) {
            $this->event = $parameters['event'];
        }

        return $this;
    }
}
