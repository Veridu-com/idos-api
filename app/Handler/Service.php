<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Handler;

use App\Command\Service\CreateNew;
use App\Command\Service\DeleteOne;
use App\Command\Service\UpdateOne;
use App\Entity\Service as ServiceEntity;
use App\Exception\Create;
use App\Exception\NotFound;
use App\Exception\Update;
use App\Exception\Validate;
use App\Factory\Event;
use App\Repository\HandlerServiceInterface;
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
     * HandlerService Repository instance.
     *
     * @var \App\Repository\HandlerServiceInterface
     */
    private $handlerServiceRepository;
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
    public static function register(ContainerInterface $container) : void {
        $container[self::class] = function (ContainerInterface $container) : HandlerInterface {
            $repositoryFactory = $container->get('repositoryFactory');

            return new \App\Handler\Service(
                $repositoryFactory
                    ->create('Service'),
                $repositoryFactory
                    ->create('HandlerService'),
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
     * @param \App\Repository\ServiceInterface        $repository
     * @param \App\Repository\HandlerServiceInterface $handlerServiceRepository
     * @param \App\Validator\Service                  $validator
     * @param \App\Factory\Event                      $eventFactory
     * @param \League\Event\Emitter                   $emitter
     *
     * @return void
     */
    public function __construct(
        ServiceInterface $repository,
        HandlerServiceInterface $handlerServiceRepository,
        ServiceValidator $validator,
        Event $eventFactory,
        Emitter $emitter
    ) {
        $this->repository                 = $repository;
        $this->handlerServiceRepository   = $handlerServiceRepository;
        $this->validator                  = $validator;
        $this->eventFactory               = $eventFactory;
        $this->emitter                    = $emitter;
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
            $this->validator->assertCompany($command->company, 'company');
            $this->validator->assertId($command->handlerServiceId, 'handlerServiceId');
            if ($command->listens) {
                $this->validator->assertArray($command->listens, 'listens');
            }

            $this->validator->assertIdentity($command->identity, 'identity');
        } catch (ValidationException $e) {
            throw new Validate\ServiceException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        if ($command->listens === null) {
            $handlerService   = $this->handlerServiceRepository->find($command->handlerServiceId);
            $command->listens = $handlerService->listens;
        }

        $now    = time();
        $entity = $this->repository->create(
            [
                'company_id'         => $command->company->id,
                'handler_service_id' => $command->handlerServiceId,
                'listens'            => $command->listens,
                'created_at'         => $now
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
            $this->validator->assertCompany($command->company, 'company');
            $this->validator->assertId($command->serviceId, 'serviceId');
            $this->validator->assertArray($command->listens, 'listens');
            $this->validator->assertIdentity($command->identity, 'identity');
        } catch (ValidationException $e) {
            throw new Validate\ServiceException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        $entity = $this->repository->findOne($command->serviceId, $command->company->id);

        $allowedListeners = $entity->handler_service()->listens;
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
     * @return int
     */
    public function handleDeleteOne(DeleteOne $command) : int {
        try {
            $this->validator->assertCompany($command->company, 'company');
            $this->validator->assertId($command->serviceId, 'serviceId');
            $this->validator->assertIdentity($command->identity, 'identity');
        } catch (ValidationException $e) {
            throw new Validate\ServiceException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        $service      = $this->repository->findOne($command->serviceId, $command->company->id);
        $rowsAffected = $this->repository->delete($command->serviceId);

        if (! $rowsAffected) {
            throw new NotFound\ServiceException('No service handlers found for deletion', 404);
        }

        $event = $this->eventFactory->create('Service\\Deleted', $service, $command->identity);
        $this->emitter->emit($event);

        return $rowsAffected;
    }
}
