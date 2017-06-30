<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Helper;

use App\Repository\RepositoryInterface;
use Illuminate\Support\Collection;

class SocialSettings {
    /**
     * Setting Repository instance.
     *
     * @var \App\Repository\RepositoryInterface
     */
    private $settingRepository;
    /**
     * Local social settings.
     *
     * @var array
     */
    private $localSettings;
    /**
     * Currently loaded Provider Name.
     *
     * @var string|null
     */
    private $providerName = null;
    /**
     * Application Key.
     *
     * @var string|null
     */
    private $appKey = null;
    /**
     * Application Secret.
     *
     * @var string|null
     */
    private $appSecret = null;
    /**
     * API Version.
     *
     * @var string|null
     */
    private $apiVersion = null;

    /**
     * Loads setting data from a database collection.
     *
     * @param \Illuminate\Support\Collection $settings
     * @param string                         $settingKey
     * @param string                         $settingSecret
     * @param string                         $settingVersion
     *
     * @return void
     */
    private function loadSettings(
        Collection $settings,
        string $settingKey,
        string $settingSecret,
        string $settingVersion
    ) : void {
        foreach ($settings as $setting) {
            switch ($setting->property) {
                case $settingKey:
                    $this->appKey = $setting->value;
                    break;
                case $settingSecret:
                    $this->appSecret = $setting->value;
                    break;
                case $settingVersion:
                    $this->apiVersion = $setting->value;
                    break;
            }
        }
    }

    /**
     * Class constructor.
     *
     * @param \App\Repository\RepositoryInterface $settingRepository
     * @param array                               $localSettings
     *
     * @return void
     */
    public function __construct(RepositoryInterface $settingRepository, array $localSettings = []) {
        $this->settingRepository = $settingRepository;
        $this->localSettings     = $localSettings;
    }

    /**
     * Retrieves settings (key, secret, version) for a provider.
     *
     * @param int    $companyId
     * @param string $credentialPubKey
     * @param string $providerName
     *
     * @return void
     */
    public function load(int $companyId, string $credentialPubKey, string $providerName) : void {
        $this->providerName = $providerName;
        $this->appKey       = null;
        $this->appSecret    = null;
        $this->apiVersion   = null;

        // hosted social application (credential based)
        $settingKey     = sprintf('%s.%s.key', $credentialPubKey, $providerName);
        $settingSecret  = sprintf('%s.%s.secret', $credentialPubKey, $providerName);
        $settingVersion = sprintf('%s.%s.version', $credentialPubKey, $providerName);

        $settings = $this->settingRepository->findByCompanyIdSectionAndProperties(
            $companyId,
            'AppTokens',
            [
                $settingKey,
                $settingSecret,
                $settingVersion
            ]
        );

        if (count($settings) > 1) {
            $this->loadSettings($settings, $settingKey, $settingSecret, $settingVersion);

            return;
        }

        // hosted social application (company based)
        $settingKey     = sprintf('%s.key', $providerName);
        $settingSecret  = sprintf('%s.secret', $providerName);
        $settingVersion = sprintf('%s.version', $providerName);

        $settings = $this->settingRepository->findByCompanyIdSectionAndProperties(
            $companyId,
            'AppTokens',
            [
                $settingKey,
                $settingSecret,
                $settingVersion
            ]
        );

        if (count($settings) > 1) {
            $this->loadSettings($settings, $settingKey, $settingSecret, $settingVersion);

            return;
        }

        if (isset($this->localSettings[$providerName])) {
            $this->appKey     = $this->localSettings[$providerName]['key'] ?? null;
            $this->appSecret  = $this->localSettings[$providerName]['secret'] ?? null;
            $this->apiVersion = $this->localSettings[$providerName]['version'] ?? null;
        }
    }

    public function getProviderList() : array {
        if (empty($this->localSettings)) {
            return [];
        }

        return array_keys($this->localSettings);
    }

    /**
     * Returns the Provider Name.
     *
     * @return string|null
     */
    public function getProviderName() : ? string {
        return $this->providerName;
    }

    /**
     * Returns the Application Key.
     *
     * @return string|null
     */
    public function getAppKey() : ? string {
        return $this->appKey;
    }

    /**
     * Returns the Application Secret.
     *
     * @return string|null
     */
    public function getAppSecret() : ? string {
        return $this->appSecret;
    }

    /**
     * Returns the API Version.
     *
     * @return string|null
     */
    public function getApiVersion() : ? string {
        return $this->apiVersion;
    }
}
