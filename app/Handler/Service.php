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
use App\Exception\NotAllowed;
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
        $container[self::class] = function (ContainerInterface $container) {
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
     * @param \App\Factory\Event               $eventFactory
     * @param \League\Event\Emitter            $emitter
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
            $this->validator->assertCompany($command->company);
            $this->validator->assertName($command->name);
            $this->validator->assertUrl($command->url);
            $this->validator->assertName($command->authUsername);
            $this->validator->assertPassword($command->authPassword);
            $this->validator->assertArray($command->listens);
            $this->validator->assertArray($command->triggers);
            $this->validator->assertAccessMode($command->access);
            $this->validator->assertFlag($command->enabled);
            $this->validator->assertIdentity($command->identity);
        } catch (ValidationException $e) {
            throw new Validate\ServiceException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        $entity = $this->repository->create(
            [
                'company_id'    => $command->company->id,
                'name'          => $command->name,
                'url'           => $command->url,
                'auth_username' => $command->authUsername,
                'auth_password' => $command->authPassword,
                'public'        => sha1('pub' . $command->company->id . microtime()),
                'private'       => sha1('priv' . $command->company->id . microtime()),
                'listens'       => $command->listens,
                'triggers'      => $command->triggers,
                'access'        => $command->access,
                'enabled'       => $command->enabled,
                'created_at'    => time()
            ]
        );

        try {
            $entity = $this->repository->save($entity);
            $event  = $this->eventFactory->create('Service\\Created', $entity, $command->identity);
            $this->emitter->emit($event);
        } catch (\Exception $e) {
            throw new Create\ServiceException('Error while trying to create a feature', 500, $e);
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
            $this->validator->assertCompany($command->company);
            $this->validator->assertId($command->serviceId);

            $input = [];

            if ($command->listens) {
                $this->validator->assertArray($command->listens);
                $input['listens'] = $command->listens;
            }

            if ($command->triggers) {
                $this->validator->AssertArray($command->triggers);
                $input['triggers'] = $command->triggers;
            }

            if ($command->url) {
                $this->validator->assertUrl($command->url);
                $input['url'] = $command->url;
            }

            if ($command->access !== null) {
                $this->validator->assertAccessMode($command->access);
                $input['access'] = $command->access;
            }

            if ($command->enabled !== null) {
                $this->validator->assertFlag($command->enabled);
                $input['enabled'] = $command->enabled;
            }

            if ($command->authUsername) {
                $this->validator->assertAuthUsername($command->authUsername);
                $input['auth_username'] = $command->authUsername;
            }

            if ($command->authPassword) {
                $this->validator->assertPassword($command->authPassword);
                $input['auth_password'] = $command->authPassword;
            }

            $this->validator->assertIdentity($command->identity);
        } catch (ValidationException $e) {
            throw new Validate\ServiceException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        $entity = $this->repository->findOne($command->serviceId, $command->company);

        // Any thoughts on a better place of verifying this
        if ($command->company->id != $entity->companyId) {
            throw new NotAllowed\ServiceException('Service doesnt belong to the given company', 403);
        }

        $backup = $entity->toArray();

        foreach ($input as $key => $value) {
            $entity->$key = $value;
        }

        if ($backup != $entity->toArray()) {
            try {
                $entity->updatedAt = time();
                $entity            = $this->repository->save($entity);
                $event             = $this->eventFactory->create('Service\\Updated', $entity, $command->identity);
                $this->emitter->emit($event);
            } catch (\Exception $e) {
                throw new Update\ServiceException('Error while trying to update a service', 500, $e);
            }
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
            $this->validator->assertCompany($command->company);
            $this->validator->assertId($command->serviceId);
            $this->validator->assertIdentity($command->identity);
        } catch (ValidationException $e) {
            throw new Validate\ServiceException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        $service = $this->repository->find($command->serviceId);

        $rowsAffected = $this->repository->deleteOne($command->serviceId, $command->company);

        if (! $rowsAffected) {
            throw new NotFound\ServiceException('No services found for deletion', 404);
        }

        $event = $this->eventFactory->create('Service\\Deleted', $service, $command->identity);
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
            $this->validator->assertCompany($command->company);
            $this->validator->assertIdentity($command->identity);
        } catch (ValidationException $e) {
            throw new Validate\ServiceException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        $services = $this->repository->getByCompany($command->company);

        $affectedRows = $this->repository->deleteByCompanyId($command->company->id);

        $event = $this->eventFactory->create('Service\\DeletedMulti', $services, $command->identity);
        $this->emitter->emit($event);

        return $affectedRows;
    }
}
