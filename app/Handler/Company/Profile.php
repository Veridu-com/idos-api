<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Handler\Company;

use App\Command\Company\Profile\DeleteOne;
use App\Exception\NotFound;
use App\Exception\Validate;
use App\Factory\Event;
use App\Handler\HandlerInterface;
use App\Repository\RepositoryInterface;
use App\Validator\User as UserValidator;
use Interop\Container\ContainerInterface;
use League\Event\Emitter;
use Respect\Validation\Exceptions\ValidationException;

/**
 * Handles CompanyProfile commands.
 */
class Profile implements HandlerInterface {
    /**
     * User Repository instance.
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
            return new \App\Handler\Company\Profile(
                $container
                    ->get('repositoryFactory')
                    ->create('user'),
                $container
                    ->get('validatorFactory')
                    ->create('user'),
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
     * Deletes a Company Profile.
     *
     * @param \App\Command\Company\Profile\DeleteOne $command
     *
     * @return int
     */
    public function handleDeleteOne(DeleteOne $command) : int {
        try {
            $this->validator->assertId($command->userId, 'userId');
            $this->validator->assertIdentity($command->identity, 'identity');
        } catch (ValidationException $exception) {
            throw new Validate\Company\ProfileException(
                $exception->getFullMessage(),
                400,
                $exception
            );
        }

        $user         = $this->repository->find($command->userId);
        $rowsAffected = $this->repository->delete($command->userId);

        if (! $rowsAffected) {
            throw new NotFound\Company\ProfileException('No profiles found for deletion', 404);
        }

        $event = $this->eventFactory->create('Company\Profile\Deleted', $user, $command->identity);
        $this->emitter->emit($event);

        return $rowsAffected;
    }
}
