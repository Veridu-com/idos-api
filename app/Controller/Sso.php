<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Controller;

use App\Factory\Command;
use App\Repository\CredentialInterface;
use App\Repository\SettingInterface;
use League\Tactician\CommandBus;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Collection;

/**
 * Handles requests to /profiles/:userName/features.
 */
class Sso implements ControllerInterface {
    /**
     * Setting Repository instance.
     *
     * @var App\Repository\SettingInterface
     */
    private $settingRepository;
    /**
     * Credential Repository instance.
     *
     * @var App\Repository\CredentialInterface
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
     * @var App\Factory\Command
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
     * @param App\Repository\SettingInterface    $settingRepository
     * @param App\Repository\CredentialInterface $credentialRepository
     * @param \Slim\Collection                   $settings
     * @param \League\Tactician\CommandBus       $commandBus
     * @param App\Factory\Command                $commandFactory
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
     * Returns the status of a given gate.
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
            'data' => in_array(
                $request->getAttribute('providerName'),
                $this->settings['sso_providers']
            )
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
     * @apiEndpointParam body string key xyz Provider key
     * @apiEndpointParam body string secret wzy Provider secret.
     * @apiEndpointParam body string ipAddress 192.168.0.1 User ip address.
     * @apiEndpointParam body string accessToken zxq Provider access token
     * @apiEndpointParam body string credentialPubKey wxz Credential public key.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @see App\Repository\DBCredental::findByPubKey
     * @see App\Repository\DBSetting::findByCompanyIdSectionAndProperties
     * @see App\Handler\Sso::handleCreateNew
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function createNew(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $requestBody = $request->getParsedBody();
        $provider    = $requestBody['providerName'];

        $availableProviders = $this->settings['sso_providers'];

        foreach ($availableProviders as $providerName) {
            if ($provider == $providerName) {
                $credentialPubKey = $requestBody['credentialPubKey'];
                $credential       = $this->credentialRepository->findByPubKey($credentialPubKey);

                $settings = $this->settingRepository->findByCompanyIdSectionAndProperties(
                    $credential->company_id,
                    'AppTokens',
                    [
                        sprintf('%s.%s.key', $credentialPubKey, $providerName),
                        sprintf('%s.%s.secret', $credentialPubKey, $providerName)
                    ]
                );

                if (count($settings) > 2) {
                    $settings = $this->settingRepository->findByCompanyIdSectionAndProperties(
                        $credential->company_id,
                        'AppTokens',
                        [
                            sprintf('%s.key', $providerName),
                            sprintf('%s.secret', $providerName)
                        ]
                    );
                }

                switch ($providerName) {
                    case 'amazon':
                        $command = $this->commandFactory->create('Sso\\CreateNewAmazon');
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
                    case 'twitter':
                        $command = $this->commandFactory->create('Sso\\CreateNewTwitter');
                        break;
                }

                foreach ($settings as $setting) {
                    if ($setting->property == sprintf('%s.%s.key', $credentialPubKey, $providerName) || sprintf('%s.key', $providerName)) {
                        $command->setParameter('key', $setting->value);
                    }

                    if ($setting->property == sprintf('%s.%s.secret', $credentialPubKey, $providerName) || sprintf('%s.secret', $providerName)) {
                        $command->setParameter('secret', $setting->value);
                    }
                }

                $command
                    ->setParameter('ipAddress', $request->getAttribute('ip_address'))
                    ->setParameter('accessToken', $requestBody['accessToken'])
                    ->setParameter('credentialPubKey', $requestBody['credentialPubKey']);

                $token = $this->commandBus->handle($command);

                $body = [
                    'status' => true,
                    'data'   => $token
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

        $body = [
            'status' => false,
            'data'   => 'Provider not found'
        ];

        $command = $this->commandFactory->create('ResponseDispatch');
        $command
            ->setParameter('statusCode', 400)
            ->setParameter('request', $request)
            ->setParameter('response', $response)
            ->setParameter('body', $body);

        return $this->commandBus->handle($command);
    }
}
