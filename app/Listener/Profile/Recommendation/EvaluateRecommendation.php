<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Listener\Profile\Recommendation;

use App\Extension\DispatchesUnhandledEvents;
use App\Extension\QueuesOnManager;
use App\Factory\Event as EventFactory;
use App\Listener\AbstractListener;
use App\Listener\ListenerInterface;
use App\Repository\Company\SettingInterface;
use App\Repository\HandlerInterface;
use App\Repository\ServiceInterface;
use App\Repository\UserInterface;
use Interop\Container\ContainerInterface;
use League\Event\Emitter;
use League\Event\EventInterface;
use Monolog\Logger;

/**
 * This listener is responsible to trigger the evaluation of a recommendation for a user.
 */
class EvaluateRecommendation extends AbstractListener {
    use DispatchesUnhandledEvents;
    use QueuesOnManager;

    /**
     * Setting Repository.
     *
     * @var \App\Repository\Company\SettingInterface
     */
    private $settingRepository;
    /**
     * Service handler repository.
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
     * User Repository.
     *
     * @var \App\Repository\UserInterface
     */
    private $userRepository;
    /**
     * Event Logger.
     *
     * @var \Monolog\Logger
     */
    private $logger;
    /**
     * Event Factory.
     *
     * @var \App\Factory\Event
     */
    private $eventFactory;
    /**
     * Event Emitter.
     *
     * @var \League\Event\Emitter
     */
    private $emitter;
    /**
     * Gearman client.
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
            $log               = $container->get('log');

            return new \App\Listener\Profile\Recommendation\EvaluateRecommendation(
                $repositoryFactory
                    ->create('Company\Setting'),
                $repositoryFactory
                    ->create('Service'),
                $repositoryFactory
                    ->create('Handler'),
                $repositoryFactory
                    ->create('User'),
                $log('Event'),
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
     * @param \App\Repository\Company\SettingInterface $settingRepository
     * @param \App\Repository\ServiceInterface         $serviceRepository
     * @param \App\Repository\HandlerInterface         $handlerRepository
     * @param \App\Repository\UserInterface            $userRepository
     * @param \Monolog\Logger                          $logger
     * @param \App\Factory\Event                       $eventFactory
     * @param \League\Event\Emitter                    $emitter
     * @param \GearmanClient                           $gearmanClient
     *
     * @return void
     */
    public function __construct(
        SettingInterface $settingRepository,
        ServiceInterface $serviceRepository,
        HandlerInterface $handlerRepository,
        UserInterface $userRepository,
        Logger $logger,
        EventFactory $eventFactory,
        Emitter $emitter,
        \GearmanClient $gearmanClient
    ) {
        $this->settingRepository = $settingRepository;
        $this->serviceRepository = $serviceRepository;
        $this->handlerRepository = $handlerRepository;
        $this->userRepository    = $userRepository;
        $this->logger            = $logger;
        $this->eventFactory      = $eventFactory;
        $this->emitter           = $emitter;
        $this->gearmanClient     = $gearmanClient;
    }

    /**
     * Handles the event.
     *
     * @param \League\Event\EventInterface $event
     *
     * @return void
     */
    public function handle(EventInterface $event) {
        $services = $this->serviceRepository->getAllByCompanyIdAndListener($event->credential->companyId, 'idos.recommendation');

        if ($services->isEmpty()) {
            $this->dispatchUnhandledEvent($event, $this->eventFactory, $this->emitter);

            return false;
        }

        try {
            $user = $this->userRepository->find($event->getUserId());
        } catch (\RuntimeException $exception) {
            // Fails silently
            return;
        }

        // tries to get by credential->public
        $settings = $this->settingRepository->findByCompanyIdSectionAndProperties(
            $event->credential->companyId,
            'recommendation',
            [
                sprintf('%s.ruleset', $event->credential->public)
            ]
        );

        if ($settings->isEmpty()) {
            // tries to get by company
            $settings = $this->settingRepository->findByCompanyIdSectionAndProperties(
                $event->credential->companyId,
                'recommendation',
                ['ruleset']
            );
        }

        // fails silently
        if ($settings->isEmpty()) {
            $this->logger->debug('Unhandled recommendation process - no rules defined.');

            return;
        }

        $this->logger->debug('Recommendation process started.');

        // parse rules
        $rules = json_decode($settings->first()->value, true);

        $success = true;

        foreach ($services as $service) {
            $handlerService = $service->handler_service();
            $handler        = $this->handlerRepository->find($handlerService->handlerId);

            // create payload
            $payload = [
                'name'    => $handlerService->name,
                'user'    => $handler->authUsername,
                'pass'    => $handler->authPassword,
                'url'     => $handlerService->url,
                'handler' => [
                    'username'  => $user->username,
                    'publickey' => $event->credential->public,
                    'rules'     => $rules
                ]
            ];

            if ($this->queueOnManager($this->gearmanClient, $payload)) {
                $this->emitter->emit($this->eventFactory->create('Manager\WorkQueued', $event));
                continue;
            }

            $success = false;
            $this->dispatchUnhandleEvent($event);
        }
    }
}
