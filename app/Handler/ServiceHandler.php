<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Handler;

use App\Command\ServiceHandler\CreateNew;
use App\Command\ServiceHandler\DeleteAll;
use App\Command\ServiceHandler\DeleteOne;
use App\Command\ServiceHandler\UpdateOne;
use App\Entity\ServiceHandler as ServiceHandlerEntity;
use App\Exception\NotFound;
use App\Repository\ServiceHandlerInterface;
use App\Validator\ServiceHandler as ServiceHandlerValidator;
use Interop\Container\ContainerInterface;

/**
 * Handles ServiceHandler commands.
 */
class ServiceHandler implements HandlerInterface {
    /**
     * ServiceHandler Repository instance.
     *
     * @var App\Repository\ServiceHandlerInterface
     */
    protected $repository;
    /**
     * ServiceHandler Validator instance.
     *
     * @var App\Validator\ServiceHandler
     */
    protected $validator;

    /**
     * {@inheritdoc}
     */
    public static function register(ContainerInterface $container) {
        $container[self::class] = function (ContainerInterface $container) : HandlerInterface {
            return new \App\Handler\ServiceHandler(
                $container
                    ->get('repositoryFactory')
                    ->create('ServiceHandler'),
                $container
                    ->get('validatorFactory')
                    ->create('ServiceHandler')
            );
        };
    }

    /**
     * Class constructor.
     *
     * @param App\Repository\ServiceHandlerInterface $repository
     * @param App\Validator\ServiceHandler           $validator
     *
     * @return void
     */
    public function __construct(
        ServiceHandlerInterface $repository,
        ServiceHandlerValidator $validator
    ) {
        $this->repository = $repository;
        $this->validator  = $validator;
    }

    /**
     * Creates a new ServiceHandler.
     *
     * @param App\Command\ServiceHandler\CreateNew $command
     *
     * @return App\Entity\ServiceHandler
     */
    public function handleCreateNew(CreateNew $command) : ServiceHandlerEntity {
        $this->validator->assertId($command->companyId);
        $this->validator->assertId($command->serviceId);
        $this->validator->assertArray($command->listens);

        $now    = time();
        $entity = $this->repository->create(
            [
                'company_id' => $command->companyId,
                'service_id' => $command->serviceId,
                'listens'    => $command->listens,
                'created_at' => $now
            ]
        );

        $entity = $this->repository->save($entity);

        return $this->repository->findOne($command->companyId, $entity->id);
    }

    /**
     * Updates a ServiceHandler.
     *
     * @param App\Command\ServiceHandler\UpdateOne $command
     *
     * @return App\Entity\ServiceHandler
     */
    public function handleUpdateOne(UpdateOne $command) : ServiceHandlerEntity {
        $this->validator->assertId($command->companyId);
        $this->validator->assertId($command->serviceHandlerId);
        $this->validator->assertArray($command->listens);

        $entity = $this->repository->findOne($command->companyId, $command->serviceHandlerId);

        $allowedListeners = $entity->service()->listens;

        // validates allowed listeners
        array_map(
            function ($listener) use ($allowedListeners) {
                if (! in_array($listener, $allowedListeners)) {
                    throw new NotFound('Listener not found on Service');
                }
            }, $command->listens
        );

        // updates listen attribute
        $entity->listens   = $command->listens;
        $entity->updatedAt = time();
        // save entity
        $this->repository->save($entity);

        return $entity;
    }

    /**
     * Deletes all service handlers ($command->companyId).
     *
     * @param App\Command\ServiceHandler\DeleteAll $command
     *
     * @return int
     */
    public function handleDeleteAll(DeleteAll $command) : int {
        $this->validator->assertId($command->companyId);

        return $this->repository->deleteByCompanyId($command->companyId);
    }

    /**
     * Deletes a ServiceHandler.
     *
     * @param App\Command\ServiceHandler\DeleteOne $command
     *
     * @throws App\Exception\NotFound
     *
     * @return int
     */
    public function handleDeleteOne(DeleteOne $command) : int {
        $this->validator->assertId($command->companyId);
        $this->validator->assertId($command->serviceHandlerId);

        $rowsAffected = $this->repository->deleteOne($command->companyId, $command->serviceHandlerId);

        if (! $rowsAffected) {
            throw new NotFound();
        }

        return $rowsAffected;
    }
}
