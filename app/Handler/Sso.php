<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Handler;

use App\Command\Sso\CreateNewAmazon;
use App\Command\Sso\CreateNewFacebook;
use App\Command\Sso\CreateNewGoogle;
use App\Command\Sso\CreateNewLinkedin;
use App\Command\Sso\CreateNewPaypal;
use App\Command\Sso\CreateNewTwitter;
use App\Exception\AppException;
use App\Factory\Command;
use App\Helper\Token;
use App\Repository\CredentialInterface;
use App\Repository\UserInterface;
use Interop\Container\ContainerInterface;
use League\Event\Emitter;
use League\Tactician\CommandBus;

/**
 * Handles Sso commands.
 */
class Sso implements HandlerInterface {
    /**
     * User repository instance.
     *
     * @var App\Repository\UserInterface
     */
    protected $userRepository;
    /**
     * Credential repository instance.
     *
     * @var App\Repository\CredentialInterface
     */
    protected $credentialRepository;
    /**
     * Event emitter instance.
     *
     * @var League\Event\Emitter
     */
    protected $emitter;
    /**
     * Provider auth service.
     *
     * @var callable
     */
    protected $service;
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
     * {@inheritdoc}
     */
    public static function register(ContainerInterface $container) {
        $container[self::class] = function (ContainerInterface $container) {
            return new \App\Handler\Sso(
                $container
                    ->get('repositoryFactory')
                    ->create('User'),
                $container
                    ->get('repositoryFactory')
                    ->create('Credential'),
                $container
                    ->get('eventEmitter'),
                $container
                    ->get('ssoAuth'),
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
     * @param App\Repository\UserInterface       $userRepository
     * @param App\Repository\CredentialInterface $credentialRepository
     * @param \League\Event\Emitter              $emitter
     * @param callable                           $service
     * @param \League\Tactician\CommandBus       $commandBus
     * @param App\Factory\Command
     *
     * @return void
     */
    public function __construct(
        UserInterface $userRepository,
        CredentialInterface $credentialRepository,
        Emitter $emitter,
        callable $service,
        CommandBus $commandBus,
        Command $commandFactory
    ) {
        $this->userRepository       = $userRepository;
        $this->credentialRepository = $credentialRepository;
        $this->emitter              = $emitter;
        $this->service              = $service;
        $this->commandBus           = $commandBus;
        $this->commandFactory       = $commandFactory;
    }

    private function createNewUser($credentialId, $role, $username) {
        $command = $this->commandFactory->create('User\\CreateNew');
        $command->setParameters(
            [
                'credentialId' => $credentialId,
                'role'         => $role,
                'username'     => $username,
            ]
        );

        return $this->commandBus->handle($command);
    }

    private function createNewSource($provider, $user, $tags, $ipAddr) {
        $command = $this->commandFactory->create('Source\\CreateNew');

        $command->setParameters(
            [
                'name'   => $provider,
                'user'   => $user,
                'tags'   => $tags,
                'ipaddr' => $ipAddr,
            ]
        );

        return $this->commandBus->handle($command);
    }

    private function createNew(
        $provider,
        $command,
        $tokenClass,
        $serviceRequestUrl,
        $decodedResponseParam,
        $eventClass
    ) {
        $service = call_user_func_array($this->service, [$provider, $command->key, $command->secret]);

        $token = new $tokenClass();
        $token->setAccessToken($command->accessToken);

        $service->getStorage()->storeAccessToken($service->service(), $token);

        try {
            $response = $service->request($serviceRequestUrl);
        } catch (\Exception $e) {
            throw new AppException('Error while contacting provider');
        }

        $decodedResponse = json_decode($response, true);

        if ($decodedResponse === null || isset($decodedResponse['error']) || isset($decodedResponse['errors'])) {
            throw new AppException('Error while trying authenticate');
        }

        $credential = $this->credentialRepository->findByPubKey($command->credentialPubKey);

        $username = $this->userRepository->getUsernameByProfileIdAndProviderNameAndCredentialId(
            $decodedResponse[$decodedResponseParam],
            $provider,
            $credential->id
        );

        if ($username) {
            $user = $this->userRepository->findByUserName($username, $credential->id);
        } else {
            $user     = $this->createNewUser($credential->id, 'user', bin2hex(openssl_random_pseudo_bytes(10)));
            $username = $user->username;
        }

        $this->createNewSource(
            $provider,
            $user,
            sprintf(
                '{
                    "profile_id" : "%s",
                    "access_token" : "%s",
                    "sso" : true
                }',
                $decodedResponse[$decodedResponseParam],
                $command->accessToken
            ),
            $command->ipAddress
        );

        $this->emitter->emit(new $eventClass($username));

        return Token::generateUserToken($username, $command->credentialPubKey, $credential->private);
    }

    /**
     * Creates a token with the amazon provider.
     *
     * @param App\Command\Sso\CreateNewAmazon $command
     *
     * @return string
     */
    public function handleCreateNewAmazon(CreateNewAmazon $command) {
        return $this->createNew(
            'amazon',
            $command,
            'OAuth\OAuth2\Token\StdOAuth2Token',
            '/user/profile',
            'user_id',
            'App\Event\Sso\CreatedAmazon'
        );
    }

    /**
     * Creates a token with the facebook provider.
     *
     * @param App\Command\Sso\CreateNewFacebook $command
     *
     * @return string
     */
    public function handleCreateNewFacebook(CreateNewFacebook $command) {
        return $this->createNew(
            'facebook',
            $command,
            'OAuth\OAuth2\Token\StdOAuth2Token',
            '/me?fields=id',
            'id',
            'App\Event\Sso\CreatedFacebook'
        );
    }

    /**
     * Creates a token with the google provider.
     *
     * @param App\Command\Sso\CreateNewGoogle $command
     *
     * @return string
     */
    public function handleCreateNewGoogle(CreateNewGoogle $command) {
        return $this->createNew(
            'google',
            $command,
            'OAuth\OAuth2\Token\StdOAuth2Token',
            'https://www.googleapis.com/oauth2/v1/userinfo',
            'id',
            'App\Event\Sso\CreatedGoogle'
        );
    }

    /**
     * Creates a token with the linkedin provider.
     *
     * @param App\Command\Sso\CreateNewLinkedin $command
     *
     * @return string
     */
    public function handleCreateNewLinkedin(CreateNewLinkedin $command) {
        return $this->createNew(
            'linkedin',
            $command,
            'OAuth\OAuth2\Token\StdOAuth2Token',
            '/people/~:(id)?format=json',
            'id',
            'App\Event\Sso\CreatedLinkedin'
        );
    }

    /**
     * Creates a token with the paypal provider.
     *
     * @param App\Command\Sso\CreateNewPaypal $command
     *
     * @return string
     */
    public function handleCreateNewPaypal(CreateNewPaypal $command) {
        return $this->createNew(
            'paypal',
            $command,
            'OAuth\OAuth2\Token\StdOAuth2Token',
            '/identity/openidconnect/userinfo/?schema=openid',
            'user_id',
            'App\Event\Sso\CreatedPaypal'
        );
    }

    /**
     * Creates a token with the twitter provider.
     *
     * @param App\Command\Sso\CreateNewTwitter $command
     *
     * @return string
     */
    public function handleCreateNewTwitter(CreateNewTwitter $command) {
        return $this->createNew(
            'twitter',
            $command,
            'OAuth\OAuth1\Token\StdOAuth1Token',
            '/account/verify_credentials.json?include_entities=false&skip_status=true',
            'id_str',
            'App\Event\Sso\CreatedTwitter'
        );
    }
}