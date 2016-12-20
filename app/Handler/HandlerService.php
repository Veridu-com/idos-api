<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Handler;

use App\Command\HandlerService\CreateNew;
use App\Command\HandlerService\DeleteAll;
use App\Command\HandlerService\DeleteOne;
use App\Command\HandlerService\UpdateOne;
use App\Entity\HandlerService as HandlerServiceEntity;
use App\Exception\Create;
use App\Exception\NotFound;
use App\Exception\Update;
use App\Exception\Validate;
use App\Factory\Event;
use App\Repository\HandlerServiceInterface as DataHandlerServiceInterface;
use App\Validator\HandlerService as HandlerServiceValidator;
use Interop\Container\ContainerInterface;
use League\Event\Emitter;
use Respect\Validation\Exceptions\ValidationException;

/**
 * Handles HandlerService commands.
 */
class HandlerService implements HandlerInterface {
    /**
     * HandlerService Repository instance.
     *
     * @var \App\Repository\HandlerServiceInterface
     */
    private $repository;
    /**
     * HandlerService Validator instance.
     *
     * @var \App\Validator\HandlerService
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
        $container[self::class] = function (ContainerInterface $container) {
            return new \App\Handler\HandlerService(
                $container
                    ->get('repositoryFactory')
                    ->create('HandlerService'),
                $container
                    ->get('validatorFactory')
                    ->create('HandlerService'),
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
     * @param \App\Repository\HandlerServiceInterface $repository
     * @param \App\Validator\HandlerService           $validator
     * @param \App\Factory\Event                      $eventFactory
     * @param \League\Event\Emitter                   $emitter
     *
     * @return void
     */
    public function __construct(
        DataHandlerServiceInterface $repository,
        HandlerServiceValidator $validator,
        Event $eventFactory,
        Emitter $emitter
    ) {
        $this->repository   = $repository;
        $this->validator    = $validator;
        $this->eventFactory = $eventFactory;
        $this->emitter      = $emitter;
    }

    /**
     * Creates a new HandlerService.
     *
     * @param \App\Command\HandlerService\CreateNew $command
     *
     * @return \App\Entity\HandlerService
     */
    public function handleCreateNew(CreateNew $command) : HandlerServiceEntity {
        try {
            $inputs = [
                'name'       => $command->name,
                'handler_id' => $command->handlerId,
                'url'        => $command->url
            ];

            $this->validator->assertId($command->handlerId);
            $this->validator->assertCompany($command->company);
            $this->validator->assertName($command->name);
            $this->validator->assertUrl($command->url);

            if (! is_null($command->privacy)) {
                $this->validator->assertId($command->privacy);
                $inputs['privacy'] = $command->privacy;
            }

            if (! is_null($command->enabled)) {
                $this->validator->assertFlag($command->enabled);
                $inputs['enabled'] = $command->enabled;
            }

            if (! is_null($command->listens)) {
                $this->validator->assertArray($command->listens);
                $inputs['listens'] = $command->listens;
            }

            $this->validator->assertIdentity($command->identity);
        } catch (ValidationException $e) {
            throw new Validate\HandlerServiceException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        $entity = $this->repository->create($inputs);

        try {
            $entity = $this->repository->save($entity);
            $entity = $this->repository->find($entity->id);
            $event  = $this->eventFactory->create('HandlerService\\Created', $entity, $command->identity);
            $this->emitter->emit($event);
        } catch (\Exception $e) {
            throw new Create\HandlerServiceException('Error while trying to create a handler', 500, $e);
        }

        return $entity;
    }

    /**
     * Updates a HandlerService.
     *
     * @param \App\Command\HandlerService\UpdateOne $command
     *
     * @return \App\Entity\HandlerService
     */
    public function handleUpdateOne(UpdateOne $command) : HandlerServiceEntity {
        try {
            $this->validator->assertCompany($command->company);
            $this->validator->assertId($command->handlerServiceId);

            $input = [];
            if (! is_null($command->name)) {
                $this->validator->assertName($command->name);
                $input['name'] = $command->name;
            }
        
            if (! is_null($command->url)) {
                $this->validator->assertUrl($command->url);
                $input['url'] = $command->url;
            }

            if (! is_null($command->enabled)) {
                $this->validator->assertFlag($command->enabled);
                $input['enabled'] = $command->enabled;
            }

            if (! is_null($command->listens)) {
                $this->validator->assertArray($command->listens);
                $input['listens'] = $command->listens;
            }

            if (! is_null($command->enabled)) {
                $this->validator->assertFlag($command->enabled);
                $input['enabled'] = $command->enabled;
            }

            if (! is_null($command->privacy)) {
                $this->validator->assertId($command->privacy);
                $input['privacy'] = $command->privacy;
            }

            $this->validator->assertIdentity($command->identity);
        } catch (ValidationException $e) {
            throw new Validate\HandlerServiceException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        $entity = $this->repository->find($command->handlerServiceId);
        $backup = $entity->toArray();

        foreach ($input as $key => $value) {
            $entity->$key = $value;
        }

        try {
            $entity            = $this->repository->save($entity);
            $event             = $this->eventFactory->create('HandlerService\\Updated', $entity, $command->identity);
            $this->emitter->emit($event);
        } catch (\Exception $e) {
            throw new Update\HandlerServiceException('Error while trying to update a service', 500, $e);
        }

        return $entity;
    }

    /**
     * Deletes a HandlerService.
     *
     * @param \App\Command\HandlerService\DeleteOne $command
     *
     * @throws \App\Exception\NotFound
     *
     * @return void
     */
    public function handleDeleteOne(DeleteOne $command) {
        try {
            $this->validator->assertCompany($command->company);
            $this->validator->assertId($command->handlerServiceId);
            $this->validator->assertIdentity($command->identity);
        } catch (ValidationException $e) {
            throw new Validate\HandlerServiceException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        $service = $this->repository->find($command->handlerServiceId);

        $rowsAffected = $this->repository->delete($command->handlerServiceId);

        if (! $rowsAffected) {
            throw new NotFound\HandlerServiceException('No services found for deletion', 404);
        }

        $event = $this->eventFactory->create('HandlerService\\Deleted', $service, $command->identity);
        $this->emitter->emit($event);
    }
}
