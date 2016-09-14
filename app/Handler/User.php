<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Handler;

use App\Command\User\CreateNew;
use App\Exception\Create;
use App\Factory\Command;
use App\Repository\UserInterface;
use Interop\Container\ContainerInterface;
use League\Event\Emitter;
use League\Tactician\CommandBus;
use App\Entity\User as UserEntity;

/**
 * Handles User commands.
 */
class User implements HandlerInterface {
    /**
     * User repository instance.
     *
     * @var App\Repository\UserInterface
     */
    protected $repository;
    /**
     * Event emitter instance.
     *
     * @var League\Event\Emitter
     */
    protected $emitter;
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
            return new \App\Handler\User(
                $container
                    ->get('repositoryFactory')
                    ->create('User'),
                $container
                    ->get('eventEmitter'),
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
     * @param App\Repository\UserInterface $repository
     * @param \League\Event\Emitter        $emitter
     * @param \League\Tactician\CommandBus
     * @param App\Factory\Command
     *
     * @return void
     */
    public function __construct(
        UserInterface $repository,
        Emitter $emitter,
        CommandBus $commandBus,
        Command $commandFactory
    ) {
        $this->repository     = $repository;
        $this->emitter        = $emitter;
        $this->commandBus     = $commandBus;
        $this->commandFactory = $commandFactory;
    }

    /**
     * Creates a user.
     *
     * @param App\Command\User\CreateNew $command
     *
     * @return App\Entity\User
     */
    public function handleCreateNew(CreateNew $command) : UserEntity {
        $user = $this->repository->create(
            [
                'credential_id' => $command->credentialId,
                'role'          => $command->role,
                'username'      => $command->username,
            ]
        );

        try {
            $user = $this->repository->save($user);
        } catch (\Exception $e) {
            throw new Create\UserException('Error while trying to create an user', 500, $e);
        }

        return $user;
    }
}
