<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Listener;

use App\Event\Manager\UnhandledEvent;
use League\Event\EventInterface;
use Monolog\Logger;

class LogFiredEventListener extends AbstractListener {
    /**
     * Event logger.
     *
     * @var Logger
     */
    private $logger;

    public function __construct(Logger $logger) {
        $this->logger = $logger;
    }

    public function handle(EventInterface $event) {
        $this->logger->debug(sprintf('%s was fired', $event->getName()));

        if (is_a($event, UnhandledEvent::class)) {
            $this->logger->debug((string) $event);
        }
    }
}
