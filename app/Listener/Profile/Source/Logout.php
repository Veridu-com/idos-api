<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Listener\Profile\Source;

use App\Entity\Profile\Source;
use App\Factory\Command;
use App\Listener\AbstractListener;
use App\Listener\ListenerInterface;
use Interop\Container\ContainerInterface;
use League\Event\EventInterface;
use League\Tactician\CommandBus;

/**
 * This listener is responsible to remove the source data that has been
 * created as a reflex of adding a new source.
 *
 * This listener is called after the \App\Event\Profile\Source\Deleted or
 * the \App\Event\Profile\Source\DeletedMulti is fired.
 */
class Logout extends AbstractListener {
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
     * Deletes raw entry of a user.
     *
     * @param \League\Event\EventInterface $event
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
     * {@inheritdoc}
     */
    public static function register(ContainerInterface $container) : void {
        $container[self::class] = function (ContainerInterface $container) : ListenerInterface {
            $log = $container->get('log');

            return new \App\Listener\Profile\Source\Logout(
                $log('Event'),
                $container
                    ->get('commandBus'),
                $container
                    ->get('commandFactory')
            );
        };
    }

    /**
     * Class constructor.
     *
     * @param \League\Tactician\CommandBus $commandBus
     * @param \App\Factory\Command         $command
     *
     * @return void
     */
    public function __construct(CommandBus $commandBus, Command $commandFactory) {
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
                $this->deleteRaw($event);
                // FIXME add $this->deleteFeature
            }
        }
    }
}
