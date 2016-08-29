<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Handler;

use App\Command\RoleAccess\CreateNew;
use App\Command\RoleAccess\DeleteAll;
use App\Command\RoleAccess\DeleteOne;
use App\Command\RoleAccess\UpdateOne;
use App\Entity\RoleAccess as RoleAccessEntity;
use App\Repository\RoleAccessInterface;
use App\Validator\RoleAccess as RoleAccessValidator;
use Interop\Container\ContainerInterface;

/**
 * Handles RoleAccess commands.
 */
class RoleAccess implements HandlerInterface {
    /**
     * RoleAccess Repository instance.
     *
     * @var App\Repository\RoleAccessInterface
     */
    protected $repository;

    /**
     * RoleAccess Validator instance.
     *
     * @var App\Validator\RoleAccess
     */
    protected $validator;

    /**
     * {@inheritdoc}
     */
    public static function register(ContainerInterface $container) {
        $container[self::class] = function (ContainerInterface $container) : HandlerInterface {
            return new \App\Handler\RoleAccess(
                $container
                    ->get('repositoryFactory')
                    ->create('RoleAccess'),
                $container
                    ->get('validatorFactory')
                    ->create('RoleAccess')
            );
        };
    }

    /**
     * Class constructor.
     *
     * @param App\Repository\RoleAccessInterface
     * @param App\Validator\RoleAccess
     *
     * @return void
     */
    public function __construct(
        RoleAccessInterface $repository,
        RoleAccessValidator $validator
    ) {
        $this->repository = $repository;
        $this->validator  = $validator;
    }

    /**
     * Creates a new child RoleAccess.
     *
     * @param App\Command\RoleAccess\CreateNew $command
     *
     * @return App\Entity\RoleAccess
     */
    public function handleCreateNew(CreateNew $command) : RoleAccessEntity {
        $this->validator->assertRoleName($command->role);
        $this->validator->assertResource($command->resource);
        $this->validator->assertAccess($command->access);
        $this->validator->assertId($command->identityId);

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

        $this->repository->save($entity);

        return $entity;
    }

    /**
     * Deletes all RoleAccess of the identity.
     *
     * @param App\Command\RoleAccess\DeleteAll $command
     *
     * @return int number of affected rows
     */
    public function handleDeleteAll(DeleteAll $command) : int {
        $this->validator->assertId($command->identityId);

        return $this->repository->deleteAllFromIdentity($command->identityId);
    }

    /**
     * Updates a RoleAccess.
     *
     * @param App\Command\RoleAccess\UpdateOne $command
     *
     * @return App\Entity\RoleAccess
     */
    public function handleUpdateOne(UpdateOne $command) : RoleAccessEntity {
        $this->validator->assertId($command->identityId);
        $this->validator->assertId($command->roleAccessId);
        $this->validator->assertAccess($command->access);

        // finds entity
        $entity            = $this->repository->findOne($command->identityId, $command->roleAccessId);
        $entity->access    = $command->access;
        $entity->updatedAt = time();

        // saves entity
        $this->repository->save($entity);

        return $entity;
    }

    /**
     * Deletes a RoleAccess.
     *
     * @param App\Command\RoleAccess\DeleteOne $command
     *
     * @return int number of affected rows
     */
    public function handleDeleteOne(DeleteOne $command) : int {
        $this->validator->assertId($command->identityId);
        $this->validator->assertId($command->roleAccessId);

        return $this->repository->deleteOne($command->identityId, $command->roleAccessId);
    }
}
