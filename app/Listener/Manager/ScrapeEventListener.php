<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Listener\Manager;

use App\Repository\CredentialInterface;
use App\Repository\SettingInterface;
use League\Event\Emitter;
use League\Event\EventInterface;

/**
 * Data Scraper Event Listener.
 */
class ScrapeEventListener extends AbstractListener {
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
     * @param string $credentialPubKey
     * @param string $sourceName
     *
     * @return array
     */
    private function loadSettings(string $credentialPubKey, string $sourceName) : array {
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

        $return = [null, null, null];
        foreach ($settings as $setting) {
            if (in_array($setting->property, [$credentialSettingKey, $providerSettingKey])) {
                $return['key'] = $setting->value;
            }

            if (in_array($setting->property, [$credentialSettingSec, $providerSettingSec])) {
                $return['secret'] = $setting->value;
            }

            if (in_array($setting->property, [$credentialSettingVer, $providerSettingVer])) {
                $return['apiVersion'] = $setting->value;
            }
        }

        return $return;
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
        Event $eventFactory,
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
        $credential = $this->credentialRepository->find($event->user->credentialId);

        try {
            $trigger = sprintf('idos:source.%s.created', strtolower($event->source->name));
            $handler = $this->serviceRepository->findXXX($credential->companyId, $trigger);

            list($appKey, $appSecret, $apiVersion) = $this->loadSettings($credential->public, $event->source->name);

            $payload = [
                'name'    => $handler->name,
                'user'    => $handler->authUsername,
                'pass'    => $handler->authPassword,
                'url'     => $handler->url,
                'handler' => [
                    'accessToken'  => $event->source->tags->accessToken,
                    'apiVersion'   => $apiVersion,
                    'appKey'       => $appKey,
                    'appSecret'    => $appSecret,
                    'providerName' => $event->source->name,
                    'publicKey'    => $credential->public,
                    'sourceId'     => $event->source->id,
                    'tokenSecret'  => $event->source->tags->tokenSecret,
                    'userName'     => $event->user->userName
                ]
            ];

            // add to manager queue
            $task = $this->gearmanClient->doBackground(
                'manager',
                json_encode($payload)
            );
            if ($this->gearmanClient->returnCode() !== \GEARMAN_SUCCESS) {
                $dispatchFailed = $this->eventFactory->create(
                    'Manager\\DispatchFailed',
                    $payload,
                    $this->gearmanClient->error()
                );
                $this->emitter->emit($dispatchFailed);
            }
        } catch (NotFound $exception) {
            // dispatch event
            $unhandledEvent = $this->eventFactory->create(
                'Manager\\UnhandledEvent',
                $event
            );
            $this->emitter->emit($unhandledEvent);
        }
    }
}
