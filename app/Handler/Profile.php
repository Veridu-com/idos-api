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

/**
 * Handles Profile commands.
 */
class Profile implements HandlerInterface {
    /**
     * User Repository instance.
     *
     * @var App\Repository\UserInterface
     */
    protected $repository;
    /**
     * Profile Validator instance.
     *
     * @var App\Validator\Profile
     */
    protected $validator;
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
            return new \App\Handler\Profile(
                $container
                    ->get('repositoryFactory')
                    ->create('User'),
                $container
                    ->get('validatorFactory')
                    ->create('Profile'),
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
     *
     * @return void
     */
    public function __construct(
        UserInterface $repository,
        ProfileValidator $validator,
        Emitter $emitter
    ) {
        $this->repository = $repository;
        $this->validator  = $validator;
        $this->emitter    = $emitter;
    }

    /**
     * Creates a new Profile.
     *
     * @param App\Command\Profile\ListAll $command
     *
     * @return Illuminate\Support\Collection
     */
    public function handleListAll(ListAll $command) : Collection {
        $this->validator->assertCompany($command->company);

        $entities = $this->repository->findByCompanyId($command->company->id);

        return $entities;
    }
}
