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
use App\Listener\AbstractListener;
use App\Listener\ListenerInterface;
use App\Repository\Company\CredentialInterface;
use App\Repository\Company\SettingInterface;
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
     * Setting Repository instance.
     *
     * @var \App\Repository\Company\SettingInterface
     */
    private $settingRepository;
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
     * Loads application Key/Secret and API Version.
     *
     * @param \App\Entity\Company\Credential $credential
     * @param string                         $sourceName
     *
     * @return array
     */
    private function loadSettings(Credential $credential, string $sourceName) : array {
        $credentialPubKey = $credential->public;

        // hosted social application (credential based)
        $credentialSettingKey = sprintf('%s.%s.key', $credentialPubKey, $sourceName);
        $credentialSettingSec = sprintf('%s.%s.secret', $credentialPubKey, $sourceName);
        $credentialSettingVer = sprintf('%s.%s.version', $credentialPubKey, $sourceName);

        // hosted social application (company based)
        $providerSettingKey = sprintf('%s.key', $sourceName);
        $providerSettingSec = sprintf('%s.secret', $sourceName);
        $providerSettingVer = sprintf('%s.version', $sourceName);

        $settings = $this->settingRepository->findByCompanyIdSectionAndProperties(
            $credential->companyId,
            'AppTokens',
            [
                $credentialSettingKey,
                $credentialSettingSec,
                $credentialSettingVer
            ]
        );

        if (count($settings) < 2) {
            $settings = $this->settingRepository->findByCompanyIdSectionAndProperties(
                $credential->companyId,
                'AppTokens',
                [
                    $providerSettingKey,
                    $providerSettingSec,
                    $providerSettingVer
                ]
            );
        }

        $appKey     = null;
        $appSecret  = null;
        $apiVersion = null;
        foreach ($settings as $setting) {
            if (in_array($setting->property, [$credentialSettingKey, $providerSettingKey])) {
                $appKey = $setting->value;
            }

            if (in_array($setting->property, [$credentialSettingSec, $providerSettingSec])) {
                $appSecret = $setting->value;
            }

            if (in_array($setting->property, [$credentialSettingVer, $providerSettingVer])) {
                $apiVersion = $setting->value;
            }
        }

        return [$appKey, $appSecret, $apiVersion];
    }

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
                    ->create('Company\Setting'),
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
     * @param \App\Repository\Company\SettingInterface    $settingRepository
     * @param \App\Factory\Event                          $eventFactory
     * @param \League\Event\Emitter                       $emitter
     * @param \GearmanClient                              $gearmanClient
     *
     * @return void
     */
    public function __construct(
        CredentialInterface $credentialRepository,
        ServiceInterface $serviceRepository,
        SettingInterface $settingRepository,
        EventFactory $eventFactory,
        Emitter $emitter,
        \GearmanClient $gearmanClient
    ) {
        $this->credentialRepository     = $credentialRepository;
        $this->serviceRepository        = $serviceRepository;
        $this->settingRepository        = $settingRepository;
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

        $credential                        = $this->credentialRepository->find($event->user->credentialId);
        [$appKey, $appSecret, $apiVersion] = $this->loadSettings($credential, $event->source->name);

        $mergePayload = [
            'appKey'     => $appKey,
            'appSecret'  => $appSecret,
            'apiVersion' => $apiVersion
        ];

        $this->queueListeningServices($credential->companyId, $event, $mergePayload);
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
