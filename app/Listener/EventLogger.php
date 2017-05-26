<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Listener;

use App\Event\Manager\UnhandledEvent;
use Interop\Container\ContainerInterface;
use League\Event\EventInterface;
use Monolog\Logger;

class EventLogger extends AbstractListener {
    /**
     * Event logger.
     *
     * @var Logger
     */
    private $logger;

    /**
     * {@inheritdoc}
     */
    public static function register(ContainerInterface $container) : void {
        $container[self::class] = function (ContainerInterface $container) : ListenerInterface {
            $log = $container->get('log');

            return new \App\Listener\EventLogger($log('Event'));
        };
    }

    /**
     * Class constructor.
     *
     * @param \Monolog\Logger $logger
     *
     * @return void
     */
    public function __construct(Logger $logger) {
        $this->logger = $logger;
    }

    /**
     * Handles the event.
     *
     * @param \League\Event\EventInterface $event
     *
     * @return void
     */
    public function handle(EventInterface $event) {
        $this->logger->debug(sprintf('%s was fired', $event->getName()));

        if (is_a($event, UnhandledEvent::class)) {
            $this->logger->debug((string) $event);
        }
    }
}
