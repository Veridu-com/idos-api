<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Controller;

use App\Exception\AppException;
use App\Factory\Command;
use App\Repository\Company\CredentialInterface;
use App\Repository\Company\SettingInterface;
use League\Tactician\CommandBus;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Collection;

/**
 * Handles requests to /sso.
 */
class Sso implements ControllerInterface {
    /**
     * Setting Repository instance.
     *
     * @var \App\Repository\Company\SettingInterface
     */
    private $settingRepository;
    /**
     * Credential Repository instance.
     *
     * @var \App\Repository\Company\CredentialInterface
     */
    private $credentialRepository;
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
     * Configurations settings.
     *
     * @var \Slim\Collection
     */
    private $settings;

    /**
     * Class constructor.
     *
     * @param \App\Repository\Company\SettingInterface    $settingRepository
     * @param \App\Repository\Company\CredentialInterface $credentialRepository
     * @param \Slim\Collection                            $settings
     * @param \League\Tactician\CommandBus                $commandBus
     * @param \App\Factory\Command                        $commandFactory
     *
     * @return void
     */
    public function __construct(
        SettingInterface $settingRepository,
        CredentialInterface $credentialRepository,
        Collection $settings,
        CommandBus $commandBus,
        Command $commandFactory
    ) {
        $this->settingRepository    = $settingRepository;
        $this->credentialRepository = $credentialRepository;
        $this->settings             = $settings;
        $this->commandBus           = $commandBus;
        $this->commandFactory       = $commandFactory;
    }

    /**
     * Lists all available SSO providers.
     *
     * @apiEndpointParam query int page 10|1 Current page
     * @apiEndpointResponse 200 schema/sso/listAll.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function listAll(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $body = [
            'data' => $this->settings['sso_providers'],
        ];

        $command = $this->commandFactory->create('ResponseDispatch');
        $command
            ->setParameter('request', $request)
            ->setParameter('response', $response)
            ->setParameter('body', $body);

        return $this->commandBus->handle($command);
    }

    /**
     * Returns the status of a given provider.
     *
     * @apiEndpointResponse 200 schema/sso/getOne.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getOne(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $body = [
            'data' => [
                'enabled' => in_array(
                    $request->getAttribute('providerName'),
                    $this->settings['sso_providers']
                )
            ]
        ];

        $command = $this->commandFactory->create('ResponseDispatch');
        $command
            ->setParameter('request', $request)
            ->setParameter('response', $response)
            ->setParameter('body', $body);

        return $this->commandBus->handle($command);
    }

    /**
     * Creates a token for the given user in the given provider.
     *
     * @apiEndpointResponse 201 schema/sso/createNew.json
     * @apiEndpointRequiredParam body string provider twitter Provider name (one of: amazon, dropbox, facebook, google, linkedin, paypal, spotify, twitter or yahoo)
     * @apiEndpointRequiredParam body string access_token abc Provider access token (oAuth 1.x and 2.x)
     * @apiEndpointRequiredParam body string credential wxz Credential public key.
     * @apiEndpointParam body string token_secret def Profiver token secret (oAuth 1.x)
     * @apiEndpointParam body string signup_hash zyd A signup hash (Dashboard login)
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @see \App\Repository\DBCredental::findByPubKey
     * @see \App\Repository\DBSetting::findByCompanyIdSectionAndProperties
     * @see \App\Handler\Sso::handleCreateNew
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function createNew(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $sourceName       = $request->getParsedBodyParam('provider');
        $credentialPubKey = $request->getParsedBodyParam('credential');
        $credential       = $this->credentialRepository->findByPubKey($credentialPubKey);

        $availableProviders = $this->settings['sso_providers'];
        if (! in_array($sourceName, $availableProviders)) {
            throw new AppException('Unsupported Provider', 400);
        }

        // hosted social application (credential based)
        $credentialSettingKey = sprintf('%s.%s.key', $credentialPubKey, $sourceName);
        $credentialSettingSec = sprintf('%s.%s.secret', $credentialPubKey, $sourceName);
        $credentialSettingVer = sprintf('%s.%s.version', $credentialPubKey, $sourceName);
        // hosted social application (company based)
        $providerSettingKey = sprintf('%s.key', $sourceName);
        $providerSettingSec = sprintf('%s.secret', $sourceName);
        $providerSettingVer = sprintf('%s.version', $sourceName);

        $settings = $this->settingRepository->getSourceTokens($credential->companyId, $credentialPubKey, $sourceName);

        switch ($sourceName) {
            case 'amazon':
                $command = $this->commandFactory->create('Sso\\CreateNewAmazon');
                break;
            case 'dropbox':
                $command = $this->commandFactory->create('Sso\\CreateNewDropbox');
                break;
            case 'facebook':
                $command = $this->commandFactory->create('Sso\\CreateNewFacebook');
                break;
            case 'google':
                $command = $this->commandFactory->create('Sso\\CreateNewGoogle');
                break;
            case 'linkedin':
                $command = $this->commandFactory->create('Sso\\CreateNewLinkedin');
                break;
            case 'paypal':
                $command = $this->commandFactory->create('Sso\\CreateNewPaypal');
                break;
            case 'spotify':
                $command = $this->commandFactory->create('Sso\\CreateNewSpotify');
                break;
            case 'twitter':
                $command = $this->commandFactory->create('Sso\\CreateNewTwitter');
                break;
            case 'yahoo':
                $command = $this->commandFactory->create('Sso\\CreateNewYahoo');
                break;
        }

        foreach ($settings as $setting) {
            if (in_array($setting->property, [$credentialSettingKey, $providerSettingKey])) {
                $command->setParameter('appKey', $setting->value);
            }

            if (in_array($setting->property, [$credentialSettingSec, $providerSettingSec])) {
                $command->setParameter('appSecret', $setting->value);
            }

            if (in_array($setting->property, [$credentialSettingVer, $providerSettingVer])) {
                $command->setParameter('apiVersion', $setting->value);
            }
        }

        $command
            ->setParameter('ipAddress', $request->getAttribute('ip_address'))
            ->setParameter('accessToken', $request->getParsedBodyParam('access_token'))
            ->setParameter('credentialPubKey', $credentialPubKey);

        $signupHash = $request->getParsedBodyParam('signup_hash');
        if ($signupHash) {
            $command->setParameter('signupHash', $signupHash);
        }

        $tokenSecret = $request->getParsedBodyParam('token_secret');
        if ($tokenSecret) {
            $command->setParameter('tokenSecret', $tokenSecret);
        }

        $body = [
            'data' => $this->commandBus->handle($command)
        ];

        $command = $this->commandFactory->create('ResponseDispatch');
        $command
            ->setParameter('statusCode', 201)
            ->setParameter('request', $request)
            ->setParameter('response', $response)
            ->setParameter('body', $body);

        return $this->commandBus->handle($command);
    }
}
