<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Listener\Company;

use App\Entity\Profile\Feature;
use App\Factory\Command;
use App\Listener;
use App\Listener\AbstractListener;
use League\Event\EventInterface;
use League\Tactician\CommandBus;

/**
 * Company Event Listener.
 */
class CompanyListener extends AbstractListener {
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
     * Class constructor.
     *
     * @param \League\Tactician\CommandBus $commandBus
     * @param \App\Factory\Command         $commandFactory
     *
     * @return void
     */
    public function __construct(
        CommandBus $commandBus,
        Command $commandFactory
    ) {
        $this->commandBus     = $commandBus;
        $this->commandFactory = $commandFactory;
    }

    /**
     * Handles events that trigger Service Handler creation.
     *
     * @param \League\Event\EventInterface $event
     *
     * @return void
     */
    public function handle(EventInterface $event) {
        $command = $this->commandFactory->create('ServiceHandler\\CreateNew');

        $command
            ->setParameter('companyId', $event->company->id)
            ->setParameter('serviceId', 1)
            ->setParameter(
                'listens',
                [
                    'idos:source.amazon.created',
                    'idos:source.dropbox.created',
                    'idos:source.facebook.created',
                    'idos:source.google.created',
                    'idos:source.linkedin.created',
                    'idos:source.paypal.created',
                    'idos:source.spotify.created',
                    'idos:source.twitter.created',
                    'idos:source.yahoo.created'
                ]
            );
        $this->commandBus->handle($command);

        $command
            ->setParameter('companyId', $event->company->id)
            ->setParameter('serviceId', 2)
            ->setParameter(
                'listens',
                [
                    'idos:raw.amazon.created',
                    'idos:raw.dropbox.created',
                    'idos:raw.facebook.created',
                    'idos:raw.google.created',
                    'idos:raw.linkedin.created',
                    'idos:raw.paypal.created',
                    'idos:raw.spotify.created',
                    'idos:raw.twitter.created',
                    'idos:raw.yahoo.created'
                ]
            );
        $this->commandBus->handle($command);

        $command
            ->setParameter('companyId', $event->company->id)
            ->setParameter('serviceId', 3)
            ->setParameter(
                'listens',
                [
                    'idos:feature.amazon.created',
                    'idos:feature.dropbox.created',
                    'idos:feature.facebook.created',
                    'idos:feature.google.created',
                    'idos:feature.linkedin.created',
                    'idos:feature.paypal.created',
                    'idos:feature.spotify.created',
                    'idos:feature.twitter.created',
                    'idos:feature.yahoo.created'
                ]
            );
        $this->commandBus->handle($command);

        $command
            ->setParameter('companyId', $event->company->id)
            ->setParameter('serviceId', 4)
            ->setParameter(
                'listens',
                [
                    'idos:feature.amazon.created',
                    'idos:feature.dropbox.created',
                    'idos:feature.facebook.created',
                    'idos:feature.google.created',
                    'idos:feature.linkedin.created',
                    'idos:feature.paypal.created',
                    'idos:feature.spotify.created',
                    'idos:feature.twitter.created',
                    'idos:feature.yahoo.created'
                ]
            );
        $this->commandBus->handle($command);

        $command
            ->setParameter('companyId', $event->company->id)
            ->setParameter('serviceId', 5)
            ->setParameter(
                'listens',
                [
                    'idos:feature.amazon.created',
                    'idos:feature.dropbox.created',
                    'idos:feature.facebook.created',
                    'idos:feature.google.created',
                    'idos:feature.linkedin.created',
                    'idos:feature.paypal.created',
                    'idos:feature.spotify.created',
                    'idos:feature.twitter.created',
                    'idos:feature.yahoo.created'
                ]
            );
        $this->commandBus->handle($command);

        $command
            ->setParameter('companyId', $event->company->id)
            ->setParameter('serviceId', 6)
            ->setParameter(
                'listens',
                [
                    'idos:feature.amazon.created',
                    'idos:feature.dropbox.created',
                    'idos:feature.facebook.created',
                    'idos:feature.google.created',
                    'idos:feature.linkedin.created',
                    'idos:feature.paypal.created',
                    'idos:feature.spotify.created',
                    'idos:feature.twitter.created',
                    'idos:feature.yahoo.created'
                ]
            );
        $this->commandBus->handle($command);

        $command
            ->setParameter('companyId', $event->company->id)
            ->setParameter('serviceId', 7)
            ->setParameter(
                'listens',
                [
                    'idos:feature.amazon.created',
                    'idos:feature.dropbox.created',
                    'idos:feature.facebook.created',
                    'idos:feature.google.created',
                    'idos:feature.linkedin.created',
                    'idos:feature.paypal.created',
                    'idos:feature.spotify.created',
                    'idos:feature.twitter.created',
                    'idos:feature.yahoo.created'
                ]
            );
        $this->commandBus->handle($command);

        $command
            ->setParameter('companyId', $event->company->id)
            ->setParameter('serviceId', 8)
            ->setParameter(
                'listens',
                [
                    'idos:feature.amazon.created',
                    'idos:feature.dropbox.created',
                    'idos:feature.facebook.created',
                    'idos:feature.google.created',
                    'idos:feature.linkedin.created',
                    'idos:feature.paypal.created',
                    'idos:feature.spotify.created',
                    'idos:feature.twitter.created',
                    'idos:feature.yahoo.created'
                ]
            );
        $this->commandBus->handle($command);

        $command
            ->setParameter('companyId', $event->company->id)
            ->setParameter('serviceId', 9)
            ->setParameter(
                'listens',
                [
                    'idos:feature.amazon.created',
                    'idos:feature.dropbox.created',
                    'idos:feature.facebook.created',
                    'idos:feature.google.created',
                    'idos:feature.linkedin.created',
                    'idos:feature.paypal.created',
                    'idos:feature.spotify.created',
                    'idos:feature.twitter.created',
                    'idos:feature.yahoo.created'
                ]
            );
        $this->commandBus->handle($command);

        $command
            ->setParameter('companyId', $event->company->id)
            ->setParameter('serviceId', 10)
            ->setParameter(
                'listens',
                [
                    'idos:feature.amazon.created',
                    'idos:feature.dropbox.created',
                    'idos:feature.facebook.created',
                    'idos:feature.google.created',
                    'idos:feature.linkedin.created',
                    'idos:feature.paypal.created',
                    'idos:feature.spotify.created',
                    'idos:feature.twitter.created',
                    'idos:feature.yahoo.created'
                ]
            );
        $this->commandBus->handle($command);

        $command
            ->setParameter('companyId', $event->company->id)
            ->setParameter('serviceId', 11)
            ->setParameter(
                'listens',
                [
                    'idos:feature.amazon.created',
                    'idos:feature.dropbox.created',
                    'idos:feature.facebook.created',
                    'idos:feature.google.created',
                    'idos:feature.linkedin.created',
                    'idos:feature.paypal.created',
                    'idos:feature.spotify.created',
                    'idos:feature.twitter.created',
                    'idos:feature.yahoo.created'
                ]
            );
        $this->commandBus->handle($command);

        $command
            ->setParameter('companyId', $event->company->id)
            ->setParameter('serviceId', 12)
            ->setParameter(
                'listens',
                [
                    'idos:feature.amazon.created',
                    'idos:feature.dropbox.created',
                    'idos:feature.facebook.created',
                    'idos:feature.google.created',
                    'idos:feature.linkedin.created',
                    'idos:feature.paypal.created',
                    'idos:feature.spotify.created',
                    'idos:feature.twitter.created',
                    'idos:feature.yahoo.created'
                ]
            );
        $this->commandBus->handle($command);

        $command
            ->setParameter('companyId', $event->company->id)
            ->setParameter('serviceId', 13)
            ->setParameter(
                'listens',
                [
                    'idos:feature.amazon.created',
                    'idos:feature.dropbox.created',
                    'idos:feature.facebook.created',
                    'idos:feature.google.created',
                    'idos:feature.linkedin.created',
                    'idos:feature.paypal.created',
                    'idos:feature.spotify.created',
                    'idos:feature.twitter.created',
                    'idos:feature.yahoo.created'
                ]
            );
        $this->commandBus->handle($command);

        $command
            ->setParameter('companyId', $event->company->id)
            ->setParameter('serviceId', 14)
            ->setParameter(
                'listens',
                [
                    'idos:feature.amazon.created',
                    'idos:feature.dropbox.created',
                    'idos:feature.facebook.created',
                    'idos:feature.google.created',
                    'idos:feature.linkedin.created',
                    'idos:feature.paypal.created',
                    'idos:feature.spotify.created',
                    'idos:feature.twitter.created',
                    'idos:feature.yahoo.created'
                ]
            );
        $this->commandBus->handle($command);

        $command
            ->setParameter('companyId', $event->company->id)
            ->setParameter('serviceId', 15)
            ->setParameter(
                'listens',
                [
                    'idos:feature.amazon.created',
                    'idos:feature.dropbox.created',
                    'idos:feature.facebook.created',
                    'idos:feature.google.created',
                    'idos:feature.linkedin.created',
                    'idos:feature.paypal.created',
                    'idos:feature.spotify.created',
                    'idos:feature.twitter.created',
                    'idos:feature.yahoo.created'
                ]
            );
        $this->commandBus->handle($command);

        $command
            ->setParameter('companyId', $event->company->id)
            ->setParameter('serviceId', 16)
            ->setParameter(
                'listens',
                [
                    'idos:feature.amazon.created',
                    'idos:feature.dropbox.created',
                    'idos:feature.facebook.created',
                    'idos:feature.google.created',
                    'idos:feature.linkedin.created',
                    'idos:feature.paypal.created',
                    'idos:feature.spotify.created',
                    'idos:feature.twitter.created',
                    'idos:feature.yahoo.created'
                ]
            );
        $this->commandBus->handle($command);

        $command
            ->setParameter('companyId', $event->company->id)
            ->setParameter('serviceId', 17)
            ->setParameter(
                'listens',
                [
                    'idos:feature.amazon.created',
                    'idos:feature.dropbox.created',
                    'idos:feature.facebook.created',
                    'idos:feature.google.created',
                    'idos:feature.linkedin.created',
                    'idos:feature.paypal.created',
                    'idos:feature.spotify.created',
                    'idos:feature.twitter.created',
                    'idos:feature.yahoo.created'
                ]
            );
        $this->commandBus->handle($command);

        $command
            ->setParameter('companyId', $event->company->id)
            ->setParameter('serviceId', 18)
            ->setParameter(
                'listens',
                [
                    'idos:feature.amazon.created',
                    'idos:feature.dropbox.created',
                    'idos:feature.facebook.created',
                    'idos:feature.google.created',
                    'idos:feature.linkedin.created',
                    'idos:feature.paypal.created',
                    'idos:feature.spotify.created',
                    'idos:feature.twitter.created',
                    'idos:feature.yahoo.created'
                ]
            );
        $this->commandBus->handle($command);

        $command
            ->setParameter('companyId', $event->company->id)
            ->setParameter('serviceId', 19)
            ->setParameter(
                'listens',
                [
                    'idos:feature.amazon.created',
                    'idos:feature.dropbox.created',
                    'idos:feature.facebook.created',
                    'idos:feature.google.created',
                    'idos:feature.linkedin.created',
                    'idos:feature.paypal.created',
                    'idos:feature.spotify.created',
                    'idos:feature.twitter.created',
                    'idos:feature.yahoo.created'
                ]
            );
        $this->commandBus->handle($command);

        $command
            ->setParameter('companyId', $event->company->id)
            ->setParameter('serviceId', 20)
            ->setParameter(
                'listens',
                [
                    'idos:feature.amazon.created',
                    'idos:feature.dropbox.created',
                    'idos:feature.facebook.created',
                    'idos:feature.google.created',
                    'idos:feature.linkedin.created',
                    'idos:feature.paypal.created',
                    'idos:feature.spotify.created',
                    'idos:feature.twitter.created',
                    'idos:feature.yahoo.created'
                ]
            );
        $this->commandBus->handle($command);

        $command
            ->setParameter('companyId', $event->company->id)
            ->setParameter('serviceId', 21)
            ->setParameter(
                'listens',
                [
                    'idos:feature.amazon.created',
                    'idos:feature.dropbox.created',
                    'idos:feature.facebook.created',
                    'idos:feature.google.created',
                    'idos:feature.linkedin.created',
                    'idos:feature.paypal.created',
                    'idos:feature.spotify.created',
                    'idos:feature.twitter.created',
                    'idos:feature.yahoo.created'
                ]
            );
        $this->commandBus->handle($command);

        $command
            ->setParameter('companyId', $event->company->id)
            ->setParameter('serviceId', 22)
            ->setParameter(
                'listens',
                [
                    'idos:feature.amazon.created',
                    'idos:feature.dropbox.created',
                    'idos:feature.facebook.created',
                    'idos:feature.google.created',
                    'idos:feature.linkedin.created',
                    'idos:feature.paypal.created',
                    'idos:feature.spotify.created',
                    'idos:feature.twitter.created',
                    'idos:feature.yahoo.created'
                ]
            );
        $this->commandBus->handle($command);

        $command
            ->setParameter('companyId', $event->company->id)
            ->setParameter('serviceId', 23)
            ->setParameter(
                'listens',
                [
                    'idos:feature.amazon.created',
                    'idos:feature.dropbox.created',
                    'idos:feature.facebook.created',
                    'idos:feature.google.created',
                    'idos:feature.linkedin.created',
                    'idos:feature.paypal.created',
                    'idos:feature.spotify.created',
                    'idos:feature.twitter.created',
                    'idos:feature.yahoo.created'
                ]
            );
        $this->commandBus->handle($command);

        $command
            ->setParameter('companyId', $event->company->id)
            ->setParameter('serviceId', 24)
            ->setParameter(
                'listens',
                [
                    'idos:feature.amazon.created',
                    'idos:feature.dropbox.created',
                    'idos:feature.facebook.created',
                    'idos:feature.google.created',
                    'idos:feature.linkedin.created',
                    'idos:feature.paypal.created',
                    'idos:feature.spotify.created',
                    'idos:feature.twitter.created',
                    'idos:feature.yahoo.created'
                ]
            );
        $this->commandBus->handle($command);

        $command
            ->setParameter('companyId', $event->company->id)
            ->setParameter('serviceId', 25)
            ->setParameter(
                'listens',
                [
                    'idos:feature.amazon.created',
                    'idos:feature.dropbox.created',
                    'idos:feature.facebook.created',
                    'idos:feature.google.created',
                    'idos:feature.linkedin.created',
                    'idos:feature.paypal.created',
                    'idos:feature.spotify.created',
                    'idos:feature.twitter.created',
                    'idos:feature.yahoo.created'
                ]
            );
        $this->commandBus->handle($command);

        $command
            ->setParameter('companyId', $event->company->id)
            ->setParameter('serviceId', 26)
            ->setParameter(
                'listens',
                [
                    'idos:feature.amazon.created',
                    'idos:feature.dropbox.created',
                    'idos:feature.facebook.created',
                    'idos:feature.google.created',
                    'idos:feature.linkedin.created',
                    'idos:feature.paypal.created',
                    'idos:feature.spotify.created',
                    'idos:feature.twitter.created',
                    'idos:feature.yahoo.created'
                ]
            );
        $this->commandBus->handle($command);

        $command
            ->setParameter('companyId', $event->company->id)
            ->setParameter('serviceId', 27)
            ->setParameter(
                'listens',
                [
                    'idos:feature.amazon.created',
                    'idos:feature.dropbox.created',
                    'idos:feature.facebook.created',
                    'idos:feature.google.created',
                    'idos:feature.linkedin.created',
                    'idos:feature.paypal.created',
                    'idos:feature.spotify.created',
                    'idos:feature.twitter.created',
                    'idos:feature.yahoo.created'
                ]
            );
        $this->commandBus->handle($command);

        $command
            ->setParameter('companyId', $event->company->id)
            ->setParameter('serviceId', 28)
            ->setParameter(
                'listens',
                [
                    'idos:feature.amazon.created',
                    'idos:feature.dropbox.created',
                    'idos:feature.facebook.created',
                    'idos:feature.google.created',
                    'idos:feature.linkedin.created',
                    'idos:feature.paypal.created',
                    'idos:feature.spotify.created',
                    'idos:feature.twitter.created',
                    'idos:feature.yahoo.created'
                ]
            );
        $this->commandBus->handle($command);
    }
}
