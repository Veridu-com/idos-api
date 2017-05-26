<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Handler\Profile;

use App\Command\Profile\Flag\CreateNew;
use App\Command\Profile\Flag\DeleteAll;
use App\Command\Profile\Flag\DeleteOne;
use App\Entity\Category;
use App\Entity\Profile\Flag as FlagEntity;
use App\Exception\AppException;
use App\Exception\Create;
use App\Exception\Update;
use App\Exception\Validate;
use App\Factory\Event;
use App\Handler\HandlerInterface;
use App\Repository\CategoryInterface;
use App\Repository\Profile\FlagInterface;
use App\Validator\Profile\Flag as FlagValidator;
use Interop\Container\ContainerInterface;
use League\Event\Emitter;
use Respect\Validation\Exceptions\ValidationException;

/**
 * Handles Flag commands.
 */
class Flag implements HandlerInterface {
    /**
     * Flag Repository instance.
     *
     * @var \App\Repository\Profile\FlagInterface
     */
    private $repository;
    /**
     * Category Repository instance.
     *
     * @var \App\Repository\CategoryInterface
     */
    private $categoryRepository;
    /**
     * Flag Validator instance.
     *
     * @var \App\Validator\Profile\Flag
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
     * Upserts a category.
     *
     * @param string $name      The name
     * @param int    $handlerId The handler identifier
     *
     * @throws \App\Exception\Update\Profile\FlagException
     *
     * @return \App\Entity\Category
     */
    private function upsertCategory(string $name, int $handlerId) : Category {
        try {
            $category = $this->categoryRepository->create(
                [
                'display_name' => $name,
                'name'         => $name,
                'handler_id'   => $handlerId,
                'type'         => 'gate'
                ]
            );

            return $this->categoryRepository->upsert($category);
        } catch (\Exception $e) {
            throw new Update\Profile\FlagException('Error while trying to upsert a Flag category', 500, $e);
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function register(ContainerInterface $container) : void {
        $container[self::class] = function (ContainerInterface $container) : HandlerInterface {
            $repositoryFactory = $container->get('repositoryFactory');

            return new \App\Handler\Profile\Flag(
                $repositoryFactory
                    ->create('Profile\Flag'),
                $repositoryFactory
                    ->create('Category'),
                $container
                    ->get('validatorFactory')
                    ->create('Profile\Flag'),
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
     * @param \App\Repository\Profile\FlagInterface $repository
     * @param \App\Repository\CategoryInterface     $categoryRepository
     * @param \App\Validator\Profile\Flag           $validator
     * @param \App\Factory\Event                    $eventFactory
     * @param \League\Event\Emitter                 $emitter
     *
     * @return void
     */
    public function __construct(
        FlagInterface $repository,
        CategoryInterface $categoryRepository,
        FlagValidator $validator,
        Event $eventFactory,
        Emitter $emitter
    ) {
        $this->repository         = $repository;
        $this->categoryRepository = $categoryRepository;
        $this->validator          = $validator;
        $this->eventFactory       = $eventFactory;
        $this->emitter            = $emitter;
    }

    /**
     * Creates a flag.
     *
     * @param \App\Command\Profile\Flag\CreateNew $command
     *
     * @throws \App\Exception\Validate\Profile\FlagException
     * @throws \App\Exception\Create\Profile\FlagException
     *
     * @see \App\Repository\DBFlag::save
     * @see \App\Repository\DBFlag::hydrateRelations
     *
     * @return \App\Entity\Profile\Flag
     */
    public function handleCreateNew(CreateNew $command) : FlagEntity {
        try {
            $this->validator->assertUser($command->user);
            $this->validator->assertHandler($command->handler);
            $this->validator->assertSlug($command->slug);

            if (isset($command->attribute)) {
                $this->validator->assertSlug($command->attribute);
            }

            $this->validator->assertCredential($command->credential);
        } catch (ValidationException $e) {
            throw new Validate\Profile\FlagException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        $entity = $this->repository->create(
            [
                'user_id'    => $command->user->id,
                'creator'    => $command->handler->id,
                'slug'       => $command->slug,
                'attribute'  => $command->attribute,
                'created_at' => time()
            ]
        );

        try {
            $this->upsertCategory($command->slug, $command->handler->id);

            $entity = $this->repository->save($entity);
            $entity = $this->repository->hydrateRelations($entity);

            $event = $this->eventFactory->create('Profile\\Flag\\Created', $entity, $command->credential);
            $this->emitter->emit($event);
        } catch (\Exception $e) {
            throw new Create\Profile\FlagException('Error while trying to create a flag', 500, $e);
        }

        return $entity;
    }

    /**
     * Deletes a Flag.
     *
     * @param \App\Command\Profile\Flag\DeleteOne $command
     *
     * @throws \App\Exception\Validate\Profile\FlagException
     * @throws \App\Exception\AppException
     *
     * @see \App\Repository\DBFlag::findOneBySlug
     * @see \App\Repository\DBFlag::delete
     *
     * @return int
     */
    public function handleDeleteOne(DeleteOne $command) : int {
        try {
            $this->validator->assertUser($command->user);
            $this->validator->assertHandler($command->handler);
            $this->validator->assertSlug($command->slug);
            $this->validator->assertCredential($command->credential);
        } catch (ValidationException $e) {
            throw new Validate\Profile\FlagException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        $entity = $this->repository->findOne($command->slug, $command->handler->id, $command->user->id);

        try {
            $affectedRows = $this->repository->delete($entity->id);

            if ($affectedRows) {
                $event = $this->eventFactory->create('Profile\\Flag\\Deleted', $entity, $command->credential);
                $this->emitter->emit($event);
            }
        } catch (\Exception $e) {
            throw new AppException('Error while deleting flag');
        }

        return $affectedRows;
    }

    /**
     * Deletes all settings ($command->userId).
     *
     * @param \App\Command\Profile\Flag\DeleteAll $command
     *
     * @throws \App\Exception\Validate\Profile\FlagException
     * @throws \App\Exception\AppException
     *
     * @see \App\Repository\DBFlag::findBy
     * @see \App\Repository\DBFlag::delete
     *
     * @return int
     */
    public function handleDeleteAll(DeleteAll $command) : int {
        try {
            $this->validator->assertUser($command->user);
            $this->validator->assertHandler($command->handler);
            $this->validator->assertCredential($command->credential);
        } catch (ValidationException $e) {
            throw new Validate\Profile\FlagException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        $entities = $this->repository->getByUserIdAndHandlerId(
            $command->handler->id,
            $command->user->id,
            $command->queryParams
        );

        $affectedRows = 0;
        try {
            foreach ($entities as $entity) {
                $affectedRows += $this->repository->delete($entity->id);
            }

            if ($affectedRows) {
                $event = $this->eventFactory->create('Profile\\Flag\\DeletedMulti', $entities, $command->credential);
                $this->emitter->emit($event);
            }
        } catch (\Exception $e) {
            throw new AppException('Error while deleting flags');
        }

        return $affectedRows;
    }
}
