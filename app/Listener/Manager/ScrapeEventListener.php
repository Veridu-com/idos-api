<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Listener\Manager;

use App\Entity\Company\Credential;
use App\Factory\Event as EventFactory;
use App\Listener;
use App\Listener\AbstractListener;
use App\Listener\QueueCompanyServiceHandlers;
use App\Repository\Company\CredentialInterface;
use App\Repository\Company\SettingInterface;
use App\Repository\ServiceHandlerInterface;
use League\Event\Emitter;
use League\Event\EventInterface;

/**
 * Data Scraper Event Listener.
 */
class ScrapeEventListener extends AbstractListener {
    use QueueCompanyServiceHandlers;

    /**
     * Credential Repository instance.
     *
     * @var App\Repository\CredentialInterface
     */
    private $credentialRepository;
    /**
     * Service Handler Repository instance.
     *
     * @var App\Repository\ServiceHandlerInterface
     */
    private $serviceHandlerRepository;
    /**
     * Setting Repository instance.
     *
     * @var App\Repository\SettingInterface
     */
    private $settingRepository;
    /**
     * Event Factory instance.
     *
     * @var App\Factory\Event
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
     * Class constructor.
     *
     * @param App\Repository\CredentialInterface     $credentialRepository
     * @param App\Repository\ServiceHandlerInterface $serviceHandlerRepository
     * @param App\Repository\SettingInterface        $settingRepository
     * @param App\Factory\Event                      $eventFactory
     * @param \League\Event\Emitter                  $emitter
     * @param \GearmanClient                         $gearmanClient
     *
     * @return void
     */
    public function __construct(
        CredentialInterface $credentialRepository,
        ServiceHandlerInterface $serviceHandlerRepository,
        SettingInterface $settingRepository,
        EventFactory $eventFactory,
        Emitter $emitter,
        \GearmanClient $gearmanClient
    ) {
        $this->credentialRepository     = $credentialRepository;
        $this->serviceHandlerRepository = $serviceHandlerRepository;
        $this->settingRepository        = $settingRepository;
        $this->eventFactory             = $eventFactory;
        $this->emitter                  = $emitter;
        $this->gearmanClient            = $gearmanClient;
    }

    /**
     * Handles events that trigger data scraping.
     *
     * @param EventInterface $event
     *
     * @return void
     */
    public function handle(EventInterface $event) {
        $valid = property_exists($event->source->tags, 'accessToken');

        if (! $valid) {
            return $this->dispatchUnhandleEvent($event);
        }

        $credential                            = $this->credentialRepository->find($event->user->credentialId);
        list($appKey, $appSecret, $apiVersion) = $this->loadSettings($credential, $event->source->name);

        $mergePaylod = [
            'appKey'     => $appKey,
            'appSecret'  => $appSecret,
            'apiVersion' => $apiVersion
        ];

        $this->queueListeningServices($credential->companyId, $event, $mergePaylod);
    }

    /**
     * Dispatches an unhandle event.
     *
     * @param EventInterface $event
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
