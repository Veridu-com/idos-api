<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Handler\Profile;

use App\Command\Profile\Gate\CreateNew;
use App\Command\Profile\Gate\DeleteAll;
use App\Command\Profile\Gate\DeleteOne;
use App\Command\Profile\Gate\UpdateOne;
use App\Command\Profile\Gate\Upsert;
use App\Entity\Category;
use App\Entity\Profile\Gate as GateEntity;
use App\Exception\Create;
use App\Exception\NotFound;
use App\Exception\Update;
use App\Exception\Validate;
use App\Factory\Event;
use App\Handler\HandlerInterface;
use App\Repository\CategoryInterface;
use App\Repository\Profile\GateInterface;
use App\Validator\Profile\Gate as GateValidator;
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
     * @var \App\Repository\Profile\GateInterface
     */
    private $repository;
    /**
     * Gate Validator instance.
     *
     * @var \App\Validator\Profile\Gate
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
            return new \App\Handler\Profile\Gate(
                $container
                    ->get('repositoryFactory')
                    ->create('Profile\Gate'),
                $container
                    ->get('repositoryFactory')
                    ->create('Category'),
                $container
                    ->get('validatorFactory')
                    ->create('Profile\Gate'),
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
     * @param \App\Repository\GateInterface     $repository
     * @param \App\Repository\CategoryInterface $categoryRepository
     * @param \App\Validator\Gate               $validator
     * @param \App\Factory\Event                $eventFactory
     * @param \League\Event\Emitter             $emitter
     *
     * @return void
     */
    public function __construct(
        GateInterface $repository,
        CategoryInterface $categoryRepository,
        GateValidator $validator,
        Event $eventFactory,
        Emitter $emitter
    ) {
        $this->repository           = $repository;
        $this->categoryRepository   = $categoryRepository;
        $this->validator            = $validator;
        $this->eventFactory         = $eventFactory;
        $this->emitter              = $emitter;
    }

    /**
     * Creates a gate.
     *
     * @param \App\Command\Profile\Gate\CreateNew $command
     *
     * @see \App\Repository\DBGate::save
     *
     * @throws \App\Exception\Validate\GateException
     * @throws \App\Exception\Create\GateException
     *
     * @return \App\Entity\Gate
     */
    public function handleCreateNew(CreateNew $command) : GateEntity {
        try {
            $this->validator->assertUser($command->user);
            $this->validator->assertService($command->service);
            $this->validator->assertName($command->name);
            $this->validator->assertFlag($command->pass);
            $this->validator->assertCredential($command->credential);

            if ($command->confidenceLevel) {
                $this->validator->assertMediumName($command->confidenceLevel);
            }
        } catch (ValidationException $e) {
            throw new Validate\Profile\GateException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        $entity = $this->repository->create(
            [
                'user_id'                => $command->user->id,
                'creator'                => $command->service->id,
                'name'                   => $command->name,
                'confidence_level'       => $command->confidenceLevel,
                'pass'                   => $this->validator->validateFlag($command->pass),
                'created_at'             => time()
            ]
        );

        try {
            $this->upsertCategory($command->name, $command->service->id);

            $entity = $this->repository->save($entity);
            $entity = $this->repository->hydrateRelations($entity);

            $event = $this->eventFactory->create('Profile\\Gate\\Created', $entity, $command->credential);
            $this->emitter->emit($event);
        } catch (\Exception $exception) {
            throw new Create\Profile\GateException('Error while trying to create a gate', 500, $exception);
        }

        return $entity;
    }

    /**
     * Updates a Gate.
     *
     * @param \App\Command\Profile\Gate\UpdateOne $command
     *
     * @see \App\Repository\DBGate::findByUserIdAndSlug
     * @see \App\Repository\DBGate::save
     *
     * @throws \App\Exception\Validate\GateException
     * @throws \App\Exception\Update\Profile\GateException
     *
     * @return \App\Entity\Gate
     */
    public function handleUpdateOne(UpdateOne $command) : GateEntity {
        try {
            $this->validator->assertUser($command->user);
            $this->validator->assertService($command->service);
            $this->validator->assertSlug($command->slug);
            $this->validator->assertFlag($command->pass);
            $this->validator->assertCredential($command->credential);
        } catch (ValidationException $e) {
            throw new Validate\Profile\GateException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        $entity = $this->repository->findBySlug($command->slug, $command->service->id, $command->user->id);

        $entity->pass      = $this->validator->validateFlag($command->pass);
        $entity->updatedAt = time();

        try {
            $entity = $this->repository->save($entity);
            $entity = $this->repository->hydrateRelations($entity);

            $event = $this->eventFactory->create('Profile\\Gate\\Updated', $entity, $command->credential);
            $this->emitter->emit($event);
        } catch (\Exception $exception) {
            throw new Update\Profile\GateException('Error while trying to update a gate', 500, $e);
        }

        return $entity;
    }

    /**
     * Updates a score for a given attribute.
     *
     * @param \App\Command\Score\Upsert $command
     *
     * @return \App\Entity\Score
     */
    public function handleUpsert(Upsert $command) : GateEntity {
        try {
            $this->validator->assertUser($command->user);
            $this->validator->assertService($command->service);
            $this->validator->assertName($command->name);

            if ($command->confidenceLevel) {
                $this->validator->assertMediumName($command->confidenceLevel);
            }
        } catch (ValidationException $e) {
            throw new Validate\Profile\GateException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        try {
            $this->repository->beginTransaction();
            $this->upsertCategory($command->name, $command->service->id);

            $entity = $this->repository->create([
                'name'             => $command->name,
                'pass'             => $command->pass,
                'confidence_level' => $command->confidenceLevel,
                'user_id'          => $command->user->id,
                'creator'          => $command->service->id,
                'created_at'       => time()
            ]);

            $entity = $this->repository->upsert($entity, ['user_id', 'creator', 'name', 'confidence_level'], [
                'updated_at' => date('Y-m-d H:i:s'),
                'pass'       => $entity->pass
            ]);
            $entity = $this->repository->findBySlug($entity->slug, $entity->creator, $entity->userId);

            $this->repository->commit();

            $entity = $this->repository->hydrateRelations($entity);

            if ($entity->createdAt === $entity->updatedAt) {
                $event = $this->eventFactory->create('Profile\\Gate\\Created', $entity, $command->credential);
            } else {
                $event = $this->eventFactory->create('Profile\\Gate\\Updated', $entity, $command->credential);
            }

            $this->emitter->emit($event);
        } catch (\Exception $e) {
            $this->repository->rollBack();
            throw new Update\Profile\GateException('Error while trying to upsert a gate', 500, $e);
        }

        return $entity;
    }

    /**
     * Upserts a category.
     *
     * @param string $name      The name
     * @param int    $serviceId The service identifier
     *
     * @throws \App\Exception\NotFound\Profile\GateException
     * @throws \App\Exception\Update\Profile\GateException
     * @throws \App\Exception\Validate\Profile\GateException
     *
     * @return \App\Entity\Category
     */
    private function upsertCategory(string $name, int $serviceId) : Category {
        try {
            $category = $this->categoryRepository->create([
                'display_name' => $name,
                'name'         => $name,
                'service_id'   => $serviceId,
                'type'         => 'gate'
            ]);

            return $this->categoryRepository->upsert($category);
        } catch (\Exception $e) {
            throw new Update\Profile\GateException('Error while trying to upsert a Gate category', 500, $e);
        }
    }

    /**
     * Deletes a Gate.
     *
     * @param \App\Command\Profile\Gate\DeleteOne $command
     *
     * @see \App\Repository\DBGate::findByUserIdAndSlug
     * @see \App\Repository\DBGate::delete
     *
     * @throws \App\Exception\Validate\GateException
     * @throws \App\Exception\NotFound\GateException
     *
     * @return int
     */
    public function handleDeleteOne(DeleteOne $command) : int {
        try {
            $this->validator->assertUser($command->user);
            $this->validator->assertService($command->service);
            $this->validator->assertSlug($command->slug);
            $this->validator->assertCredential($command->credential);
        } catch (ValidationException $e) {
            throw new Validate\Profile\GateException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        try {
            $entity       = $this->repository->findBySlug($command->slug, $command->service->id, $command->user->id);
            $affectedRows = $this->repository->delete($entity->id);

            $event = $this->eventFactory->create('Profile\\Gate\\Deleted', $entity, $command->credential);
            $this->emitter->emit($event);
        } catch (\Exception $e) {
            throw new NotFound\Profile\GateException('No gates found for deletion', 404);
        }

        return $affectedRows;
    }

    /**
     * Deletes all gates ($command->userId).
     *
     * @param \App\Command\Profile\Gate\DeleteAll $command
     *
     * @see \App\Repository\DBGate::findByUserId
     * @see \App\Repository\DBGate::deleteByUserId
     *
     * @throws \App\Exception\Validate\GateException
     *
     * @return int
     */
    public function handleDeleteAll(DeleteAll $command) : int {
        try {
            $this->validator->assertUser($command->user);
            $this->validator->assertService($command->service);
            $this->validator->assertCredential($command->credential);
        } catch (ValidationException $e) {
            throw new Validate\Profile\GateException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        $entities = $this->repository->getByServiceIdAndUserId(
            $command->service->id,
            $command->user->id,
            $command->queryParams
        );

        $affectedRows = 0;
        try {
            foreach ($entities as $entity) {
                $affectedRows += $this->repository->delete($entity->id);
            }

            $event = $this->eventFactory->create('Profile\\Gate\\DeletedMulti', $entities, $command->credential);
            $this->emitter->emit($event);
        } catch (\Exception $e) {
            throw new Update\Profile\GateException('Error while trying to delete all gates', 500, $e);
        }

        return $affectedRows;
    }
}
