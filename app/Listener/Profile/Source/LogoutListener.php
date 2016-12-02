<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Listener\Profile\Source;

use App\Entity\Profile\Source;
use App\Entity\User;
use App\Factory\Command;
use App\Listener\AbstractListener;
use League\Event\EventInterface;
use League\Tactician\CommandBus;
use Monolog\Logger;

/**
 * This listener is responsible to remove the source data that has been
 * created as a reflex of adding a new source.
 *
 * This listener is called after the \App\Event\Profile\Source\Deleted or
 * the \App\Event\Profile\Source\DeletedMulti is fired.
 */
class LogoutListener extends AbstractListener {
    /**
     * Event Logger.
     *
     * @var \Monolog\Logger
     */
    private $logger;

    /**
     * Deletes raw entry of a user.
     *
     * @param \App\Entity\User           $user   The user
     * @param \App\Entity\Profile\Source $source The source
     *
     * @return int
     */
    private function deleteRaw(EventInterface $event) {
        $command = $this->commandFactory->create('Profile\Raw\DeleteAll');
        $command
            ->setParameter('user', $event->user)
            ->setParameter('queryParams', ['source' => $event->source->name]);

        return $this->commandBus->handle($command);
    }

    /**
     * Deletes all features related to the source.
     *
     * @param \App\Entity\Profile\Source $source The source
     */
    private function deleteFeature(Source $source) {
        $command = $this->commandFactory->create('Profile\Feature\DeleteAll');
        // FIXME Profile\Feature\DeleteAll requires a service
    }

    /**
     * Class constructor.
     *
     * @param \Monolog\Logger $logger
     *
     * @return void
     */
    public function __construct(Logger $logger, CommandBus $commandBus, Command $commandFactory) {
        $this->logger         = $logger;
        $this->commandBus     = $commandBus;
        $this->commandFactory = $commandFactory;
    }

    /**
     * Handles the event.
     *
     * @param \League\Event\EventInterface $event
     *
     * @return void
     */
    public function handle(EventInterface $event) {
        if (property_exists($event, 'source')) {
            $this->deleteRaw($event);
            // FIXME add $this->deleteFeature
            return;
        }

        if (property_exists($event, 'sources')) {
            foreach ($event->sources as $source) {
                $this->deleteRaw($event->user, $source);
                // FIXME add $this->deleteFeature
            }
        }
    }
}
