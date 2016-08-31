<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Handler;

use App\Command\Token\Exchange;
use App\Event\Token\Exchanged;
use App\Event\Token\Requested;
use App\Helper\Token as TokenHelper;
use App\Repository\UserInterface;
use Interop\Container\ContainerInterface;
use League\Event\Emitter;

/**
 * Handles Token commands.
 */
class Token implements HandlerInterface {
    /**
     * User Repository instance.
     *
     * @var App\Repository\UserInterface
     */
    protected $userRepository;
    /**
     * Event emitter instance.
     *
     * @var League\Event\Emitter
     */
    protected $emitter;

    /**
     * {@inheritdoc}
     */
    public static function register(ContainerInterface $container) {
        $container[self::class] = function (ContainerInterface $container) {
            return new \App\Handler\Token(
                $container
                    ->get('repositoryFactory')
                    ->create('User'),
                $container
                    ->get('eventEmitter')
            );
        };
    }

    /**
     * Class constructor.
     *
     * @param App\Repository\UserInterface $userRepository
     *
     * @return void
     */
    public function __construct(
        UserInterface $userRepository,
        Emitter $emitter
    ) {
        $this->userRepository = $userRepository;
        $this->emitter        = $emitter;
    }

    /**
     * Creates a new attribute data in the given user.
     *
     * @param App\Command\Token\Exchange $command
     *
     * @return string
     */
    public function handleExchange(Exchange $command) : string {
        $this->validator->assertUser($command->user);
        $this->validator->assertCompany($command->actingCompany);
        $this->validator->assertCompany($command->targetCompany);
        $this->validator->assertCredential($command->credential);

        $user          = $command->user;
        $actingCompany = $command->actingCompany;
        $targetCompany = $command->targetCompany;
        $credential    = $command->credential;

        $event = new Requested($user, $actingCompany, $targetCompany, $credential);
        $this->emitter->emit($event);

        try {
            $relatedUsers    = $this->userRepository->findAllRelatedToCompany($user, $targetCompany);
            $highestRoleUser = $relatedUsers->first();

            $companyToken = TokenHelper::generateCompanyToken(
                implode(':', [$highestRoleUser->public, $highestRoleUser->username]),
                $targetCompany->public_key,
                $targetCompany->private_key
            );

            $event = new Exchanged($user, $highestRoleUser, $actingCompany, $targetCompany, $credential);
            $this->emitter->emit($event);
        } catch (\Exception $e) {
            throw new AppException('Unable to exchange the user token by a company token');
        }

        return $companyToken;
    }
}
