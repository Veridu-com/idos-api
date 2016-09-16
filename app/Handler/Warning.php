<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Handler;

use App\Command\Warning\CreateNew;
use App\Command\Warning\DeleteAll;
use App\Command\Warning\DeleteOne;
use App\Entity\Warning as WarningEntity;
use App\Event\Warning\Created;
use App\Event\Warning\Deleted;
use App\Event\Warning\DeletedMulti;
use App\Exception\Create;
use App\Exception\Validate;
use App\Repository\WarningInterface;
use App\Validator\Warning as WarningValidator;
use Interop\Container\ContainerInterface;
use League\Event\Emitter;
use Respect\Validation\Exceptions\ValidationException;

/**
 * Handles Warning commands.
 */
class Warning implements HandlerInterface {
    /**
     * Warning Repository instance.
     *
     * @var App\Repository\WarningInterface
     */
    protected $repository;
    /**
     * Warning Validator instance.
     *
     * @var App\Validator\Warning
     */
    protected $validator;
    /**
     * Event emitter instance.
     *
     * @var League\Event\Emitter
     */
    protected $emitter;

    /**
     * {@inheritdoc}
     */
    public static function register(ContainerInterface $container) {
        $container[self::class] = function (ContainerInterface $container) {
            return new \App\Handler\Warning(
                $container
                    ->get('repositoryFactory')
                    ->create('Warning'),
                $container
                    ->get('validatorFactory')
                    ->create('Warning'),
                $container
                    ->get('eventEmitter')
            );
        };
    }

    /**
     * Class constructor.
     *
     * @param App\Repository\WarningInterface $repository
     * @param App\Validator\Warning           $validator
     *
     * @return void
     */
    public function __construct(
        WarningInterface $repository,
        WarningValidator $validator,
        Emitter $emitter
    ) {
        $this->repository = $repository;
        $this->validator  = $validator;
        $this->emitter    = $emitter;
    }

    /**
     * Creates a warning.
     *
     * @param App\Command\Warning\CreateNew $command
     *
     * @return App\Entity\Warning
     */
    public function handleCreateNew(CreateNew $command) : WarningEntity {
        try {
            $this->validator->assertUser($command->user);
            $this->validator->assertService($command->service);
            $this->validator->assertName($command->name);
            $this->validator->assertName($command->reference);
        } catch (ValidationException $e) {
            throw new Validate\WarningException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        $entity = $this->repository->create(
            [
                'user_id'    => $command->user->id,
                'creator'    => $command->service->id,
                'name'       => $command->name,
                'reference'  => $command->reference,
                'created_at' => time()
            ]
        );

        try {
            $entity = $this->repository->save($entity);
            $entity = $this->repository->hydrateRelations($entity);

            $event   = new Created($entity);
            $this->emitter->emit($event);
        } catch (\Exception $exception) {
            throw new Create\WarningException('Error while trying to create a warning', 500, $e);
        }

        return $entity;
    }

    /**
     * Deletes all settings ($command->userId).
     *
     * @param App\Command\Warning\DeleteAll $command
     *
     * @return int
     */
    public function handleDeleteAll(DeleteAll $command) : int {
        try {
            $this->validator->assertUser($command->user);
            $this->validator->assertService($command->service);
        } catch (ValidationException $e) {
            throw new Validate\WarningException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        $entities = $this->repository->findBy([
            'user_id' => $command->user->id,
            'creator' => $command->service->id
        ], $command->queryParams);

        $affectedRows = 0;

        try {
            foreach ($entities as $entity) {
                $affectedRows += $this->repository->delete($entity->id);
            }

            $event        = new DeletedMulti($entities);
            $this->emitter->emit($event);
        } catch (\Exception $e) {
            throw new AppException('Error while deleting warnings');
        }

        return $affectedRows;
    }

    /**
     * Deletes a Warning.
     *
     * @param App\Command\Warning\DeleteOne $command
     *
     * @return void
     */
    public function handleDeleteOne(DeleteOne $command) : int {
        try {
            $this->validator->assertUser($command->user);
            $this->validator->assertService($command->service);
            $this->validator->assertSlug($command->slug);
        } catch (ValidationException $e) {
            throw new Validate\WarningException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        $entity = $this->repository->findOneBySlug($command->user->id, $command->service->id, $command->slug);

        try {
            $affectedRows = $this->repository->delete($entity->id);
            
            $event = new Deleted($entity);
            $this->emitter->emit($event);
        } catch (\Exception $e) {
            throw new AppException('Error while deleting warning');
        }

        return $affectedRows;
    }
}
