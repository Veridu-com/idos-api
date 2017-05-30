<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Listener\Manager;

use App\Entity\Company\Credential;
use App\Extension\QueueCompanyServiceHandlers;
use App\Factory\Event as EventFactory;
use App\Helper\SocialSettings;
use App\Listener\AbstractListener;
use App\Listener\ListenerInterface;
use App\Repository\Company\CredentialInterface;
use App\Repository\HandlerInterface;
use App\Repository\ServiceInterface;
use Interop\Container\ContainerInterface;
use League\Event\Emitter;
use League\Event\EventInterface;

/**
 * Data Scraper Event Listener.
 */
class ScrapeScheduler extends AbstractListener {
    use QueueCompanyServiceHandlers;

    /**
     * Credential Repository instance.
     *
     * @var \App\Repository\Company\CredentialInterface
     */
    private $credentialRepository;
    /**
     * Service Handler Repository instance.
     *
     * @var \App\Repository\ServiceInterface
     */
    private $serviceRepository;
    /**
     * Handler Repository instance.
     *
     * @var \App\Repository\HandlerInterface
     */
    private $handlerRepository;
    /**
     * Social Settings Helper instance.
     *
     * @var \App\Helper\SocialSettings
     */
    private $socialSettings;
    /**
     * Event Factory instance.
     *
     * @var \App\Factory\Event
     */
    private $eventFactory;
    /**
     * Event Emitter instance.
     *
     * @var \League\Event\Emitter
     */
    private $emitter;
    /**
     * Gearman Client instance.
     *
     * @var \GearmanClient
     */
    private $gearmanClient;

    /**
     * {@inheritdoc}
     */
    public static function register(ContainerInterface $container) : void {
        $container[self::class] = function (ContainerInterface $container) : ListenerInterface {
            $repositoryFactory = $container->get('repositoryFactory');

            return new \App\Listener\Manager\ScrapeScheduler(
                $repositoryFactory
                    ->create('Company\Credential'),
                $repositoryFactory
                    ->create('Service'),
                $repositoryFactory
                    ->create('Handler'),
                $container
                    ->get('socialSettings'),
                $container
                    ->get('eventFactory'),
                $container
                    ->get('eventEmitter'),
                $container
                    ->get('gearmanClient')
            );
        };
    }

    /**
     * Class constructor.
     *
     * @param \App\Repository\Company\CredentialInterface $credentialRepository
     * @param \App\Repository\ServiceInterface            $serviceRepository
     * @param \App\Repository\HandlerInterface            $handlerRepository
     * @param \App\Repository\Company\SocialSettings      $socialSettings
     * @param \App\Factory\Event                          $eventFactory
     * @param \League\Event\Emitter                       $emitter
     * @param \GearmanClient                              $gearmanClient
     *
     * @return void
     */
    public function __construct(
        CredentialInterface $credentialRepository,
        ServiceInterface $serviceRepository,
        HandlerInterface $handlerRepository,
        SocialSettings $socialSettings,
        EventFactory $eventFactory,
        Emitter $emitter,
        \GearmanClient $gearmanClient
    ) {
        $this->credentialRepository     = $credentialRepository;
        $this->serviceRepository        = $serviceRepository;
        $this->handlerRepository        = $handlerRepository;
        $this->socialSettings           = $socialSettings;
        $this->eventFactory             = $eventFactory;
        $this->emitter                  = $emitter;
        $this->gearmanClient            = $gearmanClient;
    }

    /**
     * Handles events that trigger data scraping.
     *
     * @param \League\Event\EventInterface $event
     *
     * @return void
     */
    public function handle(EventInterface $event) {
        $valid = property_exists($event->source->tags, 'access_token');

        if (! $valid) {
            $this->dispatchUnhandleEvent($event);

            return;
        }

        $credential = $this->credentialRepository->find($event->user->credentialId);

        $this->socialSettings->load($credential->companyId, $credential->public, $event->source->name);

        $this->queueListeningServices(
            $credential->companyId,
            $event,
            [
                'appKey'     => $this->socialSettings->getAppKey(),
                'appSecret'  => $this->socialSettings->getAppSecret(),
                'apiVersion' => $this->socialSettings->getApiVersion()
            ]
        );
    }

    /**
     * Dispatches an unhandle event.
     *
     * @param \League\Event\EventInterface $event
     *
     * @return void
     */
    private function dispatchUnhandleEvent(EventInterface $event) {
        $unhandledEvent = $this->eventFactory->create(
            'Manager\\UnhandledEvent',
            $event
        );
        $this->emitter->emit($unhandledEvent);
    }
}
