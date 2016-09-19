<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Handler;

use App\Command\Gate\CreateNew;
use App\Command\Gate\DeleteAll;
use App\Command\Gate\DeleteOne;
use App\Command\Gate\UpdateOne;
use App\Command\Gate\Upsert;
use App\Entity\Gate as GateEntity;
use App\Event\Gate\Created;
use App\Event\Gate\Deleted;
use App\Event\Gate\DeletedMulti;
use App\Event\Gate\Updated;
use App\Exception\Create;
use App\Exception\NotFound;
use App\Exception\Update;
use App\Exception\Validate;
use App\Repository\GateInterface;
use App\Validator\Gate as GateValidator;
use Interop\Container\ContainerInterface;
use League\Event\Emitter;
use Respect\Validation\Exceptions\ValidationException;

/**
 * Handles Gate commands.
 */
class Gate implements HandlerInterface {
    /**
     * Gate Repository instance.
     *
     * @var App\Repository\GateInterface
     */
    protected $repository;
    /**
     * Gate Validator instance.
     *
     * @var App\Validator\Gate
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
            return new \App\Handler\Gate(
                $container
                    ->get('repositoryFactory')
                    ->create('Gate'),
                $container
                    ->get('validatorFactory')
                    ->create('Gate'),
                $container
                    ->get('eventEmitter')
            );
        };
    }

    /**
     * Class constructor.
     *
     * @param App\Repository\GateInterface $repository
     * @param App\Validator\Gate           $validator
     *
     * @return void
     */
    public function __construct(
        GateInterface $repository,
        GateValidator $validator,
        Emitter $emitter
    ) {
        $this->repository = $repository;
        $this->validator  = $validator;
        $this->emitter    = $emitter;
    }

    /**
     * Creates a gate.
     *
     * @param App\Command\Gate\CreateNew $command
     *
     * @return App\Entity\Gate
     */
    public function handleCreateNew(CreateNew $command) : GateEntity {
        try {
            $this->validator->assertUser($command->user);
            $this->validator->assertService($command->service);
            $this->validator->assertName($command->name);
            $this->validator->assertFlag($command->pass);
        } catch (ValidationException $e) {
            throw new Validate\GateException(
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
                'pass'       => $this->validator->validateFlag($command->pass),
                'created_at' => time()
            ]
        );

        try {
            $entity = $this->repository->save($entity);
            $entity = $this->repository->hydrateRelations($entity);

            $event = new Created($entity);
            $this->emitter->emit($event);
        } catch (\Exception $e) {
            throw new Create\GateException('Error while trying to create a gate', 500, $e);
        }

        return $entity;
    }

    /**
     * Updates a Gate.
     *
     * @param App\Command\Gate\UpdateOne $command
     *
     * @return App\Entity\Gate
     */
    public function handleUpdateOne(UpdateOne $command) : GateEntity {
        try {
            $this->validator->assertUser($command->user);
            $this->validator->assertService($command->service);
            $this->validator->assertSlug($command->slug);
            $this->validator->assertFlag($command->pass);
        } catch (ValidationException $e) {
            throw new Validate\GateException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        $entity = $this->repository->findOneBySlug($command->user->id, $command->service->id, $command->slug);

        $entity->pass      = $this->validator->validateFlag($command->pass);
        $entity->updatedAt = time();

        try {
            $entity = $this->repository->save($entity);
            $entity = $this->repository->hydrateRelations($entity);

            $event = new Updated($entity);
            $this->emitter->emit($event);
        } catch (\Exception $e) {
            throw new Update\GateException('Error while trying to update a gate', 500, $e);
        }

        return $entity;
    }

    /**
     * Updates a score for a given attribute.
     *
     * @param App\Command\Score\Upsert $command
     *
     * @return App\Entity\Score
     */
    public function handleUpsert(Upsert $command) : GateEntity {
        $this->validator->assertUser($command->user);
        $this->validator->assertService($command->service);
        $this->validator->assertName($command->name);
        $this->validator->assertFlag($command->pass);

        $entity    = null;
        $inserting = false;
        try {
            $entity = $this->repository->findOneByName($command->user->id, $command->service->id, $command->name);

            $entity->pass      = $this->validator->validateFlag($command->pass);
            $entity->updatedAt = time();
        } catch (NotFound $e) {
            $inserting = true;

            $entity = $this->repository->create(
                [
                    'user_id'    => $command->user->id,
                    'creator'    => $command->service->id,
                    'name'       => $command->name,
                    'pass'       => $command->pass,
                    'created_at' => time()
                ]
            );
        }

        try {
            $entity = $this->repository->save($entity);
            $entity = $this->repository->hydrateRelations($entity);

            if ($inserting) {
                $event = new Created($entity);
            } else {
                $event = new Updated($entity);
            }

            $this->emitter->emit($event);
        } catch (\Exception $e) {
            throw new Update\GateException('Error while trying to upsert a gate', 500, $e);
        }

        return $entity;
    }

    /**
     * Deletes all gates ($command->userId).
     *
     * @param App\Command\Gate\DeleteAll $command
     *
     * @return int
     */
    public function handleDeleteAll(DeleteAll $command) : int {
        try {
            $this->validator->assertUser($command->user);
            $this->validator->assertService($command->service);
        } catch (ValidationException $e) {
            throw new Validate\GateException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        $entities = $this->repository->findBy(
            [
            'user_id' => $command->user->id,
            'creator' => $command->service->id
            ], $command->queryParams
        );

        $affectedRows = 0;

        try {
            foreach ($entities as $entity) {
                $affectedRows += $this->repository->delete($entity->id);
            }

            $event = new DeletedMulti($entities);
            $this->emitter->emit($event);
        } catch (\Exception $e) {
            throw new Update\GateException('Error while trying to delete all gates', 500, $e);
        }

        return $affectedRows;
    }

    /**
     * Deletes a Gate.
     *
     * @param App\Command\Gate\DeleteOne $command
     *
     * @return void
     */
    public function handleDeleteOne(DeleteOne $command) : int {
        try {
            $this->validator->assertUser($command->user);
            $this->validator->assertService($command->service);
            $this->validator->assertSlug($command->slug);
        } catch (ValidationException $e) {
            throw new Validate\GateException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        try {
            $entity = $this->repository->findOneBySlug($command->user->id, $command->service->id, $command->slug);

            $affectedRows = $this->repository->delete($entity->id);

            $event = new Deleted($entity);
            $this->emitter->emit($event);
        } catch (\Exception $e) {
            throw new NotFound\GateException('No gates found for deletion', 404);
        }

        return $affectedRows;
    }
}
