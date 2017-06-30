<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Handler;

use App\Command\User\CreateNew;
use App\Entity\User as UserEntity;
use App\Exception\Create;
use App\Exception\Validate;
use App\Factory\Event;
use App\Repository\RepositoryInterface;
use App\Validator\User as UserValidator;
use Interop\Container\ContainerInterface;
use League\Event\Emitter;
use Respect\Validation\Exceptions\ValidationException;

/**
 * Handles User commands.
 */
class User implements HandlerInterface {
    /**
     * User repository instance.
     *
     * @var \App\Repository\RepositoryInterface
     */
    private $repository;
    /**
     * User Validator instance.
     *
     * @var \App\Validator\User
     */
    private $validator;
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
     * {@inheritdoc}
     */
    public static function register(ContainerInterface $container) : void {
        $container[self::class] = function (ContainerInterface $container) : HandlerInterface {
            return new \App\Handler\User(
                $container
                    ->get('repositoryFactory')
                    ->create('User'),
                $container
                    ->get('validatorFactory')
                    ->create('User'),
                $container
                    ->get('eventFactory'),
                $container
                    ->get('eventEmitter')
            );
        };
    }

    /**
     * Class constructor.
     *
     * @param \App\Repository\RepositoryInterface $repository
     * @param \App\Validator\User                 $validator
     * @param \App\Factory\Event                  $eventFactory
     * @param \League\Event\Emitter               $emitter
     *
     * @return void
     */
    public function __construct(
        RepositoryInterface $repository,
        UserValidator $validator,
        Event $eventFactory,
        Emitter $emitter
    ) {
        $this->repository   = $repository;
        $this->validator    = $validator;
        $this->eventFactory = $eventFactory;
        $this->emitter      = $emitter;
    }

    /**
     * Creates a user.
     *
     * @param \App\Command\User\CreateNew $command
     *
     * @throws \App\Exception\Validate\UserException
     * @throws \App\Exception\Create\UserException
     *
     * @return \App\Entity\User
     */
    public function handleCreateNew(CreateNew $command) : UserEntity {
        try {
            $this->validator->assertCredential($command->credential, 'credential');
            $this->validator->assertId($command->credential->id, 'credentialId');
            $this->validator->assertUserName($command->username, 'username');
        } catch (ValidationException $exception) {
            throw new Validate\UserException(
                $exception->getFullMessage(),
                400,
                $exception
            );
        }

        $user = $this->repository->create(
            [
                'credential_id' => $command->credential->id,
                'role'          => $command->role,
                'username'      => $command->username,
            ]
        );

        try {
            $user  = $this->repository->save($user);
            $event = $this->eventFactory->create('User\Created', $user);
            $this->emitter->emit($event);
        } catch (\Exception $exception) {
            throw new Create\UserException('Error while trying to create an user', 500, $exception);
        }

        return $user;
    }
}
