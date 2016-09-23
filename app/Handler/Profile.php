<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Handler;

use App\Command\Profile\ListAll;
use App\Repository\UserInterface;
use App\Validator\Profile as ProfileValidator;
use Illuminate\Support\Collection;
use Interop\Container\ContainerInterface;
use League\Event\Emitter;
use App\Factory\Event;

/**
 * Handles Profile commands.
 */
class Profile implements HandlerInterface {
    /**
     * User Repository instance.
     *
     * @var App\Repository\UserInterface
     */
    private $repository;
    /**
     * Profile Validator instance.
     *
     * @var App\Validator\Profile
     */
    private $validator;
    /**
     * Event factory instance.
     *
     * @var App\Factory\Event
     */
    private $eventFactory;
    /**
     * Event emitter instance.
     *
     * @var League\Event\Emitter
     */
    private $emitter;

    /**
     * {@inheritdoc}
     */
    public static function register(ContainerInterface $container) {
        $container[self::class] = function (ContainerInterface $container) {
            return new \App\Handler\Profile(
                $container
                    ->get('repositoryFactory')
                    ->create('User'),
                $container
                    ->get('validatorFactory')
                    ->create('Profile'),
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
     * @param App\Repository\UserInterface $repository
     * @param App\Validator\Profile        $validator
     * @param App\Factory\Event            $eventFactory
     * @param \League\Event\Emitter        $emitter
     *
     * @return void
     */
    public function __construct(
        UserInterface $repository,
        ProfileValidator $validator,
        Event $eventFactory,
        Emitter $emitter
    ) {
        $this->repository   = $repository;
        $this->validator    = $validator;
        $this->eventFactory = $eventFactory;
        $this->emitter      = $emitter;
    }

    /**
     * Creates a new Profile.
     *
     * @param App\Command\Profile\ListAll $command
     *
     * @see App\Repository\DBProfile::findByCompanyId
     *
     * @return Illuminate\Support\Collection
     */
    public function handleListAll(ListAll $command) : Collection {
        $this->validator->assertCompany($command->company);

        $entities = $this->repository->findByCompanyId($command->company->id);

        return $entities;
    }
}
