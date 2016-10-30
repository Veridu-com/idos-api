<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\Metric;

use App\Command\AbstractCommand;

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
     *
     * @return \App\Command\Metric\CreateNew
     */
    public function setParameters(array $parameters) : self {
        if (isset($parameters['event'])) {
            $this->event = $parameters['event'];
        }

        return $this;
    }
}
