<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Handler;

use App\Command\Handler\CreateNew;
use App\Command\Handler\DeleteAll;
use App\Command\Handler\DeleteOne;
use App\Command\Handler\UpdateOne;
use App\Entity\Handler as HandlerEntity;
use App\Exception\Create;
use App\Exception\NotAllowed;
use App\Exception\NotFound;
use App\Exception\Update;
use App\Exception\Validate;
use App\Factory\Event;
use App\Repository\RepositoryInterface;
use App\Validator\Handler as HandlerValidator;
use Interop\Container\ContainerInterface;
use League\Event\Emitter;
use Respect\Validation\Exceptions\ValidationException;

/**
 * Handles Handler commands.
 */
class Handler implements HandlerInterface {
    /**
     * Handler Repository instance.
     *
     * @var \App\Repository\RepositoryInterface
     */
    private $repository;
    /**
     * Handler Validator instance.
     *
     * @var \App\Validator\Handler
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
            return new \App\Handler\Handler(
                $container
                    ->get('repositoryFactory')
                    ->create('Handler'),
                $container
                    ->get('validatorFactory')
                    ->create('Handler'),
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
     * @param \App\Validator\Handler              $validator
     * @param \App\Factory\Event                  $eventFactory
     * @param \League\Event\Emitter               $emitter
     *
     * @return void
     */
    public function __construct(
        RepositoryInterface $repository,
        HandlerValidator $validator,
        Event $eventFactory,
        Emitter $emitter
    ) {
        $this->repository   = $repository;
        $this->validator    = $validator;
        $this->eventFactory = $eventFactory;
        $this->emitter      = $emitter;
    }

    /**
     * Creates a new Handler.
     *
     * @param \App\Command\Handler\CreateNew $command
     *
     * @return \App\Entity\Handler
     */
    public function handleCreateNew(CreateNew $command) : HandlerEntity {
        try {
            $this->validator->assertCompany($command->company, 'company');
            $this->validator->assertName($command->name, 'name');
            $this->validator->assertName($command->authUsername, 'authUsername');
            $this->validator->assertPassword($command->authPassword, 'authPassword');
            $this->validator->assertFlag($command->enabled, 'enabled');
            $this->validator->assertIdentity($command->identity, 'identity');
        } catch (ValidationException $exception) {
            throw new Validate\HandlerException(
                $exception->getFullMessage(),
                400,
                $exception
            );
        }

        $entity = $this->repository->create(
            [
                'company_id'    => $command->company->id,
                'name'          => $command->name,
                'auth_username' => $command->authUsername,
                'auth_password' => $command->authPassword,
                'public'        => sha1('pub' . $command->company->id . microtime()),
                'private'       => sha1('priv' . $command->company->id . microtime()),
                'enabled'       => $command->enabled,
                'created_at'    => time()
            ]
        );

        try {
            $entity = $this->repository->save($entity);
            $event  = $this->eventFactory->create('Handler\Created', $entity, $command->identity);
            $this->emitter->emit($event);
        } catch (\Exception $exception) {
            throw new Create\HandlerException('Error while trying to create a handler', 500, $exception);
        }

        return $entity;
    }

    /**
     * Updates a Handler.
     *
     * @param \App\Command\Handler\UpdateOne $command
     *
     * @return \App\Entity\Handler
     */
    public function handleUpdateOne(UpdateOne $command) : HandlerEntity {
        try {
            $this->validator->assertCompany($command->company, 'company');
            $this->validator->assertId($command->handlerId, 'handlerId');

            $input = [];

            if ($command->name !== null) {
                $this->validator->assertName($command->name, 'name');
                $input['name'] = $command->name;
            }

            if ($command->authUsername !== null) {
                $this->validator->assertName($command->authUsername, 'authUsername');
                $input['auth_username'] = $command->authUsername;
            }

            if ($command->authPassword !== null) {
                $this->validator->assertPassword($command->authPassword, 'authPassword');
                $input['auth_password'] = $command->authPassword;
            }

            if ($command->enabled !== null) {
                $this->validator->assertFlag($command->enabled, 'enabled');
                $input['enabled'] = $command->enabled;
            }

            $this->validator->assertIdentity($command->identity, 'identity');
        } catch (ValidationException $exception) {
            throw new Validate\HandlerException(
                $exception->getFullMessage(),
                400,
                $exception
            );
        }

        $entity = $this->repository->find($command->handlerId);

        // Any thoughts on a better place of verifying this
        if ($command->company->id !== $entity->companyId) {
            throw new NotAllowed\HandlerException("Handler doesn't belong to the given company", 403);
        }

        $backup = $entity->toArray();

        foreach ($input as $key => $value) {
            $entity->$key = $value;
        }

        if ($backup !== $entity->toArray()) {
            try {
                $entity->updatedAt = time();
                $entity            = $this->repository->save($entity);
                $event             = $this->eventFactory->create('Handler\Updated', $entity, $command->identity);
                $this->emitter->emit($event);
            } catch (\Exception $exception) {
                throw new Update\HandlerException('Error while trying to update a service', 500, $exception);
            }
        }

        return $entity;
    }

    /**
     * Deletes a Handler.
     *
     * @param \App\Command\Handler\DeleteOne $command
     *
     * @throws \App\Exception\NotFound
     *
     * @return void
     */
    public function handleDeleteOne(DeleteOne $command) {
        try {
            $this->validator->assertCompany($command->company, 'company');
            $this->validator->assertId($command->handlerId, 'handlerId');
            $this->validator->assertIdentity($command->identity, 'identity');
        } catch (ValidationException $exception) {
            throw new Validate\HandlerException(
                $exception->getFullMessage(),
                400,
                $exception
            );
        }

        $service      = $this->repository->find($command->handlerId);
        $rowsAffected = $this->repository->deleteOne($command->handlerId, $command->company);

        if (! $rowsAffected) {
            throw new NotFound\HandlerException('No services found for deletion', 404);
        }

        $event = $this->eventFactory->create('Handler\Deleted', $service, $command->identity);
        $this->emitter->emit($event);
    }

    /**
     * Deletes all service handlers ($command->companyId).
     *
     * @param \App\Command\Handler\DeleteAll $command
     *
     * @return int
     */
    public function handleDeleteAll(DeleteAll $command) : int {
        try {
            $this->validator->assertCompany($command->company, 'company');
            $this->validator->assertIdentity($command->identity, 'identity');
        } catch (ValidationException $exception) {
            throw new Validate\HandlerException(
                $exception->getFullMessage(),
                400,
                $exception
            );
        }

        $services = $this->repository->getByCompany($command->company);

        $affectedRows = $this->repository->deleteByCompanyId($command->company->id);

        $event = $this->eventFactory->create('Handler\DeletedMulti', $services, $command->identity);
        $this->emitter->emit($event);

        return $affectedRows;
    }
}
