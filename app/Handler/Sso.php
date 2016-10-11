<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Handler;

use App\Command\Sso\CreateNew;
use App\Command\Sso\CreateNewAmazon;
use App\Command\Sso\CreateNewFacebook;
use App\Command\Sso\CreateNewGoogle;
use App\Command\Sso\CreateNewLinkedin;
use App\Command\Sso\CreateNewPaypal;
use App\Command\Sso\CreateNewTwitter;
use App\Entity\Company as CompanyEntity;
use App\Entity\Company\Credential;
use App\Entity\Company\Member as MemberEntity;
use App\Entity\Profile\Source as SourceEntity;
use App\Entity\User;
use App\Exception\Create;
use App\Exception\NotFound;
use App\Exception\Validate\Company\InvitationException;
use App\Factory\Command;
use App\Factory\Event;
use App\Helper\Token;
use App\Repository\Company\CredentialInterface;
use App\Repository\Company\InvitationInterface;
use App\Repository\Company\MemberInterface;
use App\Repository\CompanyInterface;
use App\Repository\IdentityInterface;
use App\Repository\UserInterface;
use Interop\Container\ContainerInterface;
use League\Event\Emitter;
use League\Tactician\CommandBus;

/**
 * Handles Sso commands.
 */
class Sso implements HandlerInterface {
    /**
     * User Repository instance.
     *
     * @var \App\Repository\UserInterface
     */
    private $userRepository;
    /**
     * Credential Repository instance.
     *
     * @var \App\Repository\Company\CredentialInterface
     */
    private $credentialRepository;
    /**
     * Identity Repository instance.
     *
     * @var \App\Repository\IdentityInterface
     */
    private $identityRepository;
    /**
     * Event factory instance.
     *
     * @var \App\Factory\Event
     */
    private $eventFactory;
    /**
     * Event emitter instance.
     *
     * @var \League\Event\Emitter
     */
    private $emitter;
    /**
     * Provider auth service.
     *
     * @var callable
     */
    private $service;
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
                    ->create('Company\Credential'),
                $container
                    ->get('repositoryFactory')
                    ->create('Company\Member'),
                $container
                    ->get('repositoryFactory')
                    ->create('Company'),
                $container
                    ->get('repositoryFactory')
                    ->create('Company\Invitation'),
                $container
                    ->get('repositoryFactory')
                    ->create('Identity'),
                $container
                    ->get('eventFactory'),
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
     * @param \App\Factory\Command
     * @param \App\Repository\UserInterface               $userRepository
     * @param \App\Repository\Company\CredentialInterface $credentialRepository
     * @param \App\Repository\Company\MemberInterface     $memberRepository
     * @param \App\Repository\Company                     $companyRepository
     * @param \App\Repository\Company\InvitationInterface $invitationRepository
     * @param \App\Repository\IdentityInterface           $identityRepository
     * @param \App\Factory\Event                          $eventFactory
     * @param \League\Event\Emitter                       $emitter
     * @param callable                                    $service
     * @param \League\Tactician\CommandBus                $commandBus
     * @param \App\Factory\Command                        $commandFactory
     *
     * @return void
     */
    public function __construct(
        UserInterface $userRepository,
        CredentialInterface $credentialRepository,
        MemberInterface $memberRepository,
        CompanyInterface $companyRepository,
        InvitationInterface $invitationRepository,
        IdentityInterface $identityRepository,
        Event $eventFactory,
        Emitter $emitter,
        callable $service,
        CommandBus $commandBus,
        Command $commandFactory
    ) {
        $this->userRepository           = $userRepository;
        $this->credentialRepository     = $credentialRepository;
        $this->memberRepository         = $memberRepository;
        $this->companyRepository        = $companyRepository;
        $this->invitationRepository     = $invitationRepository;
        $this->identityRepository       = $identityRepository;
        $this->eventFactory             = $eventFactory;
        $this->emitter                  = $emitter;
        $this->service                  = $service;
        $this->commandBus               = $commandBus;
        $this->commandFactory           = $commandFactory;
    }

    /**
     * Creates a new user.
     *
     * @param \App\Entity\Credential $credential The credential
     * @param string                 $role       The role
     * @param string                 $username   The username
     *
     * @return \App\Entity\User The created user
     */
    private function createNewUser(Credential $credential, string $role, string $username) : User {
        $command = $this->commandFactory->create('User\\CreateNew');
        $command->setParameters(
            [
                'credential' => $credential,
                'role'       => $role,
                'username'   => $username,
            ]
        );

        return $this->commandBus->handle($command);
    }

    /**
     * Creates a new source.
     *
     * @param string           $sourceName The provider
     * @param \App\Entity\User $user       The user
     * @param array            $tags       The tags
     * @param string           $ipAddr     The ip address
     *
     * @return \App\Entity\Profile\Source The created source
     */
    private function createNewSource(
        string $sourceName,
        User $user,
        array $tags,
        Credential $credential,
        string $ipAddr
    ) : SourceEntity {
        $command = $this->commandFactory->create('Profile\\Source\\CreateNew');

        $command->setParameters(
            [
                'name'       => $sourceName,
                'user'       => $user,
                'tags'       => $tags,
                'credential' => $credential,
                'ipaddr'     => $ipAddr,
            ]
        );

        return $this->commandBus->handle($command);
    }

    private function createNewMembership(
        CompanyEntity $company,
        int $identityId,
        string $role,
        string $ipaddr
    ) : MemberEntity {
        $command = $this->commandFactory->create('Company\\Member\\CreateNew');

        $command->setParameter('company', $company);
        $command->setParameter('ipaddr', $ipaddr);
        $command->setParameters(
            [
                'identity_id' => $identityId,
                'role'        => $role
            ]
        );

        return $this->commandBus->handle($command);
    }

    /**
     * Creates a new sso source and a new user token.
     *
     * @param string                       $sourceName           The provider
     * @param \App\Command\AbstractCommand $command              The CreateNew command for the provider
     * @param callable|string              $tokenClass           The oauth token class
     * @param string                       $serviceRequestUrl    The provider url that will be used to get the user id
     * @param string                       $decodedResponseParam The response parameter that holds the user's id
     * @param callable|string              $eventClass           The createNew event class name to be emitted
     *
     * @throws \App\Exception\AppException        Exception thrown in case of error contacting the provider
     * @throws \App\Exception\Create\SsoException
     *
     * @see \App\Repository\DBCredential::findByPubKey
     * @see \App\Repository\DBUser::getUsernameByProfileIdAndProviderNameAndCredentialId
     * @see \App\Repository\DBUser::findByUsername
     *
     * @return array An array of generated tokens (userToken and optionally identityToken)
     */
    private function createNew(
        string $sourceName,
        CreateNew $command,
        string $tokenClass,
        string $serviceRequestUrl,
        string $decodedResponseParam,
        string $eventClass
    ) : array {
        $service = call_user_func_array($this->service, [$sourceName, $command->appKey, $command->appSecret]);

        $token = new $tokenClass();
        $token->setAccessToken($command->accessToken);
        if (isset($command->tokenSecret)) {
            $token->setAccessTokenSecret($command->tokenSecret);
        }

        $service->getStorage()->storeAccessToken($service->service(), $token);

        try {
            $response = $service->request($serviceRequestUrl);
        } catch (\Exception $e) {
            throw new Create\SsoException('Error while trying to contact provider', 500, $e);
        }

        $decodedResponse = json_decode($response, true);

        if ($decodedResponse === null || isset($decodedResponse['error']) || isset($decodedResponse['errors'])) {
            throw new Create\SsoException('Error while trying to authenticate', 500);
        }

        $credential = $this->credentialRepository->findByPubKey($command->credentialPubKey);

        try {
            $identity = $this->identityRepository->findOneBySourceNameAndProfileId(
                $sourceName,
                $decodedResponse[$decodedResponseParam],
                $command->appKey ?: 'Veridu'
            );
        } catch (NotFound $e) {
            $identityCommand = $this->commandFactory->create('Identity\\CreateNew');
            $identityCommand
                ->setParameter('sourceName', $sourceName)
                ->setParameter('profileId', $decodedResponse[$decodedResponseParam])
                ->setParameter('appKey', $identityCommand->appKey ?: 'Veridu');

            $identity = $this->commandBus->handle($identityCommand);
        }

        try {
            $user = $this->userRepository->findOneByIdentityIdAndCredentialId($identity->id, $credential->id);
        } catch (NotFound $e) {
            $user = $this->createNewUser($credential, 'user', bin2hex(openssl_random_pseudo_bytes(10)));
            $this->userRepository->assignIdentityToUser($user->id, $identity->id);
        }

        $array = [
            'profile_id'   => $decodedResponse[$decodedResponseParam],
            'access_token' => $command->accessToken,
            'sso'          => true
        ];

        if (isset($command->tokenSecret)) {
            $array['token_secret'] = $command->tokenSecret;
        }

        $this->createNewSource(
            $sourceName,
            $user,
            $array,
            $credential,
            $command->ipAddress
        );

        $event = $this->eventFactory->create($eventClass, $user->username);
        $this->emitter->emit($event);

        $tokens = [
            'user_token' => Token::generateUserToken(
                $user->username,
                $command->credentialPubKey,
                $credential->private
            )
        ];

        if ($credential->special) {
            $tokens['identity_token'] = Token::generateIdentityToken($identity->publicKey, $identity->privateKey);

            // if it is a signup on a Dashboard
            if (! empty($command->signupHash)) {
                try {
                    $invitation = $this->invitationRepository->findOneByHash($command->signupHash);

                    if ($invitation->expires < strftime('%Y-%m-%d', time())) {
                        throw new InvitationException('Expired invitation.');
                    }
                    if ($invitation->voided) {
                        throw new InvitationException('Invitation already used.');
                    }

                    $company = $this->companyRepository->find($invitation->companyId);

                    // if memberId is null and invitation is not expired
                    // a member should be created for this identity
                    if (is_null($invitation->memberId)) {
                        try {
                            $member = $this->memberRepository->findMembership($identity->id, $company->id);
                        } catch (NotFound $e) {
                            // if can't find membership, creates
                            $member               = $this->createNewMembership($company, $identity->id, $invitation->role, $command->ipAddress);
                            $invitation->memberId = $member->id;
                            $invitation->voided   = true;
                            // saves modified invitation
                            $this->invitationRepository->save($invitation);
                        }

                    }
                } catch (NotFound $e) {
                    throw new InvitationException('Invalid invitation code.');
                }

            }
        }

        return $tokens;
    }

    /**
     * Creates a token with the amazon provider.
     *
     * @param \App\Command\Sso\CreateNewAmazon $command
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
            'Sso\\CreatedAmazon'
        );
    }

    /**
     * Creates a token with the facebook provider.
     *
     * @param \App\Command\Sso\CreateNewFacebook $command
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
            'Sso\\CreatedFacebook'
        );
    }

    /**
     * Creates a token with the google provider.
     *
     * @param \App\Command\Sso\CreateNewGoogle $command
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
            'Sso\\CreatedGoogle'
        );
    }

    /**
     * Creates a token with the linkedin provider.
     *
     * @param \App\Command\Sso\CreateNewLinkedin $command
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
            'Sso\\CreatedLinkedin'
        );
    }

    /**
     * Creates a token with the paypal provider.
     *
     * @param \App\Command\Sso\CreateNewPaypal $command
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
            'Sso\\CreatedPaypal'
        );
    }

    /**
     * Creates a token with the twitter provider.
     *
     * @param \App\Command\Sso\CreateNewTwitter $command
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
            'Sso\\CreatedTwitter'
        );
    }
}
