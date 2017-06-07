<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Handler\User;

use App\Command\User\RoleAccess\CreateNew;
use App\Command\User\RoleAccess\DeleteAll;
use App\Command\User\RoleAccess\DeleteOne;
use App\Command\User\RoleAccess\UpdateOne;
use App\Entity\User\RoleAccess as RoleAccessEntity;
use App\Exception\Create;
use App\Exception\NotFound;
use App\Exception\Update;
use App\Exception\Validate;
use App\Factory\Event;
use App\Handler\HandlerInterface;
use App\Repository\User\RoleAccessInterface;
use App\Validator\User\RoleAccess as RoleAccessValidator;
use Interop\Container\ContainerInterface;
use League\Event\Emitter;
use Respect\Validation\Exceptions\ValidationException;

/**
 * Handles RoleAccess commands.
 */
class RoleAccess implements HandlerInterface {
    /**
     * RoleAccess Repository instance.
     *
     * @var \App\Repository\User\RoleAccessInterface
     */
    private $repository;
    /**
     * RoleAccess Validator instance.
     *
     * @var \App\Validator\User\RoleAccess
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
            return new \App\Handler\User\RoleAccess(
                $container
                    ->get('repositoryFactory')
                    ->create('User\RoleAccess'),
                $container
                    ->get('validatorFactory')
                    ->create('User\RoleAccess'),
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
     * @param \App\Repository\User\RoleAccessInterface $repository
     * @param \App\Validator\User\RoleAccess           $validator
     * @param \App\Factory\Event                       $eventFactory
     * @param \League\Event\Emitter                    $emitter
     *
     * @return void
     */
    public function __construct(
        RoleAccessInterface $repository,
        RoleAccessValidator $validator,
        Event $eventFactory,
        Emitter $emitter
    ) {
        $this->repository   = $repository;
        $this->validator    = $validator;
        $this->eventFactory = $eventFactory;
        $this->emitter      = $emitter;
    }

    /**
     * Creates a new child RoleAccess.
     *
     * @param \App\Command\User\RoleAccess\CreateNew $command
     *
     * @throws \App\Exception\Validate\User\RoleAccessException
     * @throws \App\Exception\Create\User\RoleAccessException
     *
     * @return \App\Entity\User\RoleAccess
     */
    public function handleCreateNew(CreateNew $command) : RoleAccessEntity {
        try {
            $this->validator->assertRoleName($command->role, 'role');
            $this->validator->assertResource($command->resource, 'resource');
            $this->validator->assertAccess($command->access, 'access');
            $this->validator->assertId($command->identityId, 'identityId');
        } catch (ValidationException $e) {
            throw new Validate\User\RoleAccessException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        $now = time();

        $entity = $this->repository->create(
            [
                'role'        => $command->role,
                'resource'    => $command->resource,
                'access'      => $command->access,
                'identity_id' => $command->identityId,
                'created_at'  => $now,
                'updated_at'  => $now
            ]
        );

        try {
            $entity = $this->repository->save($entity);
            $event  = $this->eventFactory->create('User\\RoleAccess\\Created', $entity);
            $this->emitter->emit($event);
        } catch (\Exception $e) {
            throw new Create\User\RoleAccessException('Error while trying to create a role access', 500, $e);
        }

        return $entity;
    }

    /**
     * Deletes all RoleAccess of the identity.
     *
     * @param \App\Command\User\RoleAccess\DeleteAll $command
     *
     * @throws \App\Exception\Validate\User\RoleAccessException
     *
     * @return int number of affected rows
     */
    public function handleDeleteAll(DeleteAll $command) : int {
        try {
            $this->validator->assertId($command->identityId, 'identityId');
        } catch (ValidationException $e) {
            throw new Validate\User\RoleAccessException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        $roleAccesses = $this->repository->findByIdentity($command->identityId);

        $rowsAffected = $this->repository->deleteAllFromIdentity($command->identityId);

        $event = $this->eventFactory->create('User\\RoleAccess\\DeletedMulti', $roleAccesses);
        $this->emitter->emit($event);

        return $rowsAffected;
    }

    /**
     * Updates a RoleAccess.
     *
     * @param \App\Command\User\RoleAccess\UpdateOne $command
     *
     * @throws \App\Exception\Validate\User\RoleAccessException
     * @throws \App\Exception\Update\User\RoleAccessException
     *
     * @return \App\Entity\User\RoleAccess
     */
    public function handleUpdateOne(UpdateOne $command) : RoleAccessEntity {
        try {
            $this->validator->assertId($command->identityId, 'identityId');
            $this->validator->assertId($command->roleAccessId, 'roleAccessId');
            $this->validator->assertAccess($command->access, 'access');
        } catch (ValidationException $e) {
            throw new Validate\User\RoleAccessException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        // finds entity
        $entity            = $this->repository->findOne($command->identityId, $command->roleAccessId);
        $entity->access    = $command->access;
        $entity->updatedAt = time();

        // saves entity
        try {
            $entity = $this->repository->save($entity);
            $event  = $this->eventFactory->create('User\\RoleAccess\\Updated', $entity);
            $this->emitter->emit($event);
        } catch (\Exception $e) {
            throw new Update\User\RoleAccessException('Error while trying to update a role access', 500, $e);
        }

        return $entity;
    }

    /**
     * Deletes a RoleAccess.
     *
     * @param \App\Command\User\RoleAccess\DeleteOne $command
     *
     * @throws \App\Exception\Validate\User\RoleAccessException
     * @throws \App\Exception\NotFound\User\RoleAccessException
     *
     * @return void
     */
    public function handleDeleteOne(DeleteOne $command) {
        try {
            $this->validator->assertId($command->identityId, 'identityId');
            $this->validator->assertId($command->roleAccessId, 'roleAccessId');
        } catch (ValidationException $e) {
            throw new Validate\User\RoleAccessException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        $roleAccess   = $this->repository->findOne($command->identityId, $command->roleAccessId);
        $rowsAffected = $this->repository->deleteOne($command->identityId, $command->roleAccessId);

        if (! $rowsAffected) {
            throw new NotFound\User\RoleAccessException('No role accesses found for deletion', 404);
        }

        $event = $this->eventFactory->create('User\\RoleAccess\\Deleted', $roleAccess);
        $this->emitter->emit($event);
    }
}
