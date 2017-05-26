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
     * @param \App\Entity\User           $user
     * @param \App\Entity\Profile\Source $source
     *
     * @return int
     */
    private function deleteRaw(User $user, Source $source) : int {
        $command = $this->commandFactory->create('Profile\Raw\DeleteAll');
        $command
            ->setParameter('user', $user)
            ->setParameter('queryParams', ['source' => $source->name]);

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
            return new \App\Listener\Profile\Source\Logout(
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
     * @param \App\Factory\Command         $commandFactory
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
            $this->deleteRaw($event->user, $event->source);
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
