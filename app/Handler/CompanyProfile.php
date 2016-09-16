<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Handler;

use App\Command\CompanyProfile\DeleteOne;
use App\Event\CompanyProfile\Deleted;
use App\Exception\NotFound;
use App\Exception\Validate;
use App\Repository\UserInterface;
use App\Validator\User as UserValidator;
use Interop\Container\ContainerInterface;
use League\Event\Emitter;
use Respect\Validation\Exceptions\ValidationException;

/**
 * Handles CompanyProfile commands.
 */
class CompanyProfile implements HandlerInterface {
    /**
     * User Repository instance.
     *
     * @var App\Repository\UserInterface
     */
    protected $repository;
    /**
     * User Validator instance.
     *
     * @var App\Validator\User
     */
    protected $validator;
    /**
     * Event emitter instance.
     *
     * @var \League\Event\Emitter
     */
    protected $emitter;

    /**
     * {@inheritdoc}
     */
    public static function register(ContainerInterface $container) {
        $container[self::class] = function (ContainerInterface $container) : HandlerInterface {
            return new \App\Handler\CompanyProfile(
                $container
                    ->get('repositoryFactory')
                    ->create('user'),
                $container
                    ->get('validatorFactory')
                    ->create('user'),
                $container
                    ->get('eventEmitter')
            );
        };
    }

    /**
     * Class constructor.
     *
     * @param App\Repository\UserInterface $repository
     * @param App\Validator\User           $validator
     * @param \League\Event\Emitter        $emitter
     *
     * @return void
     */
    public function __construct(
        UserInterface $repository,
        UserValidator $validator,
        Emitter $emitter
    ) {
        $this->repository = $repository;
        $this->validator  = $validator;
        $this->emitter    = $emitter;
    }

    /**
     * Deletes a CompanyProfile.
     *
     * @param App\Command\CompanyProfile\DeleteOne $command
     *
     * @return int
     */
    public function handleDeleteOne(DeleteOne $command) : int {
        try {
            $this->validator->assertId($command->userId);
        } catch (ValidationException $e) {
            throw new Validate\CompanyProfileException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        $user         = $this->repository->find($command->userId);
        $rowsAffected = $this->repository->delete($command->userId);

        if (! $rowsAffected) {
            throw new NotFound\CompanyProfileException('No profiles found for deletion', 404);
        }

        $event = new Deleted($user);
        $this->emitter->emit($event);

        return $rowsAffected;
    }
}
