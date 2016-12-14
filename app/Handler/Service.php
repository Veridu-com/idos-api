<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Handler;

use App\Command\Service\CreateNew;
use App\Command\Service\DeleteAll;
use App\Command\Service\DeleteOne;
use App\Command\Service\UpdateOne;
use App\Entity\Service as ServiceEntity;
use App\Exception\Create;
use App\Exception\NotFound;
use App\Exception\Update;
use App\Exception\Validate;
use App\Factory\Event;
use App\Repository\ServiceInterface;
use App\Validator\Service as ServiceValidator;
use Interop\Container\ContainerInterface;
use League\Event\Emitter;
use Respect\Validation\Exceptions\ValidationException;

/**
 * Handles Service commands.
 */
class Service implements HandlerInterface {
    /**
     * Service Repository instance.
     *
     * @var \App\Repository\ServiceInterface
     */
    private $repository;
    /**
     * Service Validator instance.
     *
     * @var \App\Validator\Service
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
    public static function register(ContainerInterface $container) {
        $container[self::class] = function (ContainerInterface $container) : HandlerInterface {
            return new \App\Handler\Service(
                $container
                    ->get('repositoryFactory')
                    ->create('Service'),
                $container
                    ->get('validatorFactory')
                    ->create('Service'),
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
     * @param \App\Repository\ServiceInterface $repository
     * @param \App\Validator\Service           $validator
     * @param \App\Factory\Event                      $eventFactory
     * @param \League\Event\Emitter                   $emitter
     *
     * @return void
     */
    public function __construct(
        ServiceInterface $repository,
        ServiceValidator $validator,
        Event $eventFactory,
        Emitter $emitter
    ) {
        $this->repository   = $repository;
        $this->validator    = $validator;
        $this->eventFactory = $eventFactory;
        $this->emitter      = $emitter;
    }

    /**
     * Creates a new Service.
     *
     * @param \App\Command\Service\CreateNew $command
     *
     * @return \App\Entity\Service
     */
    public function handleCreateNew(CreateNew $command) : ServiceEntity {
        try {
            $this->validator->assertId($command->companyId);
            $this->validator->assertId($command->handlerId);
            $this->validator->assertArray($command->listens);
            $this->validator->assertIdentity($command->identity);
        } catch (ValidationException $e) {
            throw new Validate\ServiceException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        $now    = time();
        $entity = $this->repository->create(
            [
                'company_id' => $command->companyId,
                'service_id' => $command->handlerId,
                'listens'    => $command->listens,
                'created_at' => $now
            ]
        );

        try {
            $entity = $this->repository->save($entity);
            $entity = $this->repository->hydrateRelations($entity);
            $event  = $this->eventFactory->create('Service\\Created', $entity, $command->identity);
            $this->emitter->emit($event);
        } catch (\Exception $e) {
            throw new Create\ServiceException('Error while trying to create a service handler', 500, $e);
        }

        return $entity;
    }

    /**
     * Updates a Service.
     *
     * @param \App\Command\Service\UpdateOne $command
     *
     * @return \App\Entity\Service
     */
    public function handleUpdateOne(UpdateOne $command) : ServiceEntity {
        try {
            $this->validator->assertId($command->companyId);
            $this->validator->assertId($command->serviceHandlerId);
            $this->validator->assertArray($command->listens);
            $this->validator->assertIdentity($command->identity);
        } catch (ValidationException $e) {
            throw new Validate\ServiceException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        $entity = $this->repository->findOne($command->serviceHandlerId, $command->companyId);

        $allowedListeners = $entity->service()->listens;

        // validates allowed listeners
        array_map(
            function ($listener) use ($allowedListeners) {
                if (! in_array($listener, $allowedListeners)) {
                    throw new NotFound\ServiceException('Listener not found on Service', 404);
                }
            }, $command->listens
        );

        // updates listen attribute
        $entity->listens   = $command->listens;
        $entity->updatedAt = time();
        // save entity
        try {
            $entity = $this->repository->save($entity);
            $entity = $this->repository->hydrateRelations($entity);
            $event  = $this->eventFactory->create('Service\\Updated', $entity, $command->identity);
            $this->emitter->emit($event);
        } catch (\Exception $e) {
            throw new Update\ServiceException('Error while trying to update a service handler', 500, $e);
        }

        return $entity;
    }

    /**
     * Deletes a Service.
     *
     * @param \App\Command\Service\DeleteOne $command
     *
     * @throws \App\Exception\NotFound
     *
     * @return void
     */
    public function handleDeleteOne(DeleteOne $command) {
        try {
            $this->validator->assertId($command->companyId);
            $this->validator->assertId($command->serviceHandlerId);
            $this->validator->assertIdentity($command->identity);
        } catch (ValidationException $e) {
            throw new Validate\ServiceException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        $serviceHandler = $this->repository->find($command->serviceHandlerId);

        $rowsAffected = $this->repository->deleteOne($command->companyId, $command->serviceHandlerId);

        if (! $rowsAffected) {
            throw new NotFound\ServiceException('No service handlers found for deletion', 404);
        }

        $event = $this->eventFactory->create('Service\\Deleted', $serviceHandler, $command->identity);
        $this->emitter->emit($event);
    }

    /**
     * Deletes all service handlers ($command->companyId).
     *
     * @param \App\Command\Service\DeleteAll $command
     *
     * @return int
     */
    public function handleDeleteAll(DeleteAll $command) : int {
        try {
            $this->validator->assertId($command->companyId);
            $this->validator->assertIdentity($command->identity);
        } catch (ValidationException $e) {
            throw new Validate\ServiceException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        $serviceHandlers = $this->repository->getByCompanyId($command->companyId);

        $rowsAffected = $this->repository->deleteByCompanyId($command->companyId);

        $event = $this->eventFactory->create('Service\\DeletedMulti', $serviceHandlers, $command->identity);
        $this->emitter->emit($event);

        return $rowsAffected;
    }
}
