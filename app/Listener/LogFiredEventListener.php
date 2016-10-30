<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Listener;

use League\Event\EventInterface;
use App\Factory\Command;
use League\Tactician\CommandBus;
use Monolog\Logger;

class LogFiredEventListener extends AbstractListener {
    /**
     * Command Bus instance.
     *
     * @var \League\Tactician\CommandBus
     */
    private $commandBus;
    /**
     * Command Factory instance.
     *
     * @var \App\Factory\Command
     */
    private $commandFactory;
    /**
     * Event logger.
     *
     * @var Logger
     */
    private $logger;

    public function __construct(CommandBus $commandBus, Command $commandFactory, Logger $logger) {
        $this->commandBus     = $commandBus;
        $this->commandFactory = $commandFactory;
        $this->logger         = $logger;
    }

    public function handle(EventInterface $event) {
        $createMetricCommand = $this->commandFactory->create('Metric\CreateNew');
        $createMetricCommand->setParameter('event', $event);

        $this->commandBus->handle($createMetricCommand);
        $this->logger->debug(sprintf('%s was fired', $event->getName()));
    }
}
