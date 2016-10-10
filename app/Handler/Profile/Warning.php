<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Handler\Profile;

use App\Command\Profile\Warning\CreateNew;
use App\Command\Profile\Warning\DeleteAll;
use App\Command\Profile\Warning\DeleteOne;
use App\Entity\Profile\Warning as WarningEntity;
use App\Exception\Create;
use App\Exception\Validate;
use App\Factory\Event;
use App\Handler\HandlerInterface;
use App\Repository\Profile\WarningInterface;
use App\Validator\Profile\Warning as WarningValidator;
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
     * @var \App\Repository\Profile\WarningInterface
     */
    private $repository;
    /**
     * Warning Validator instance.
     *
     * @var \App\Validator\Profile\Warning
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
            return new \App\Handler\Profile\Warning(
                $container
                    ->get('repositoryFactory')
                    ->create('Profile\Warning'),
                $container
                    ->get('validatorFactory')
                    ->create('Profile\Warning'),
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
     * @param \App\Repository\Profile\WarningInterface $repository
     * @param \App\Validator\Profile\Warning           $validator
     * @param \App\Factory\Event               $eventFactory
     * @param \League\Event\Emitter           $emitter
     *
     * @return void
     */
    public function __construct(
        WarningInterface $repository,
        WarningValidator $validator,
        Event $eventFactory,
        Emitter $emitter
    ) {
        $this->repository   = $repository;
        $this->validator    = $validator;
        $this->eventFactory = $eventFactory;
        $this->emitter      = $emitter;
    }

    /**
     * Creates a warning.
     *
     * @param \App\Command\Profile\Warning\CreateNew $command
     *
     * @throws \App\Exception\Validate\Profile\WarningException
     * @throws \App\Exception\Create\Profile\WarningException
     *
     * @see \App\Repository\DBWarning::save
     * @see \App\Repository\DBWarning::hydrateRelations
     *
     * @return \App\Entity\Profile\Warning
     */
    public function handleCreateNew(CreateNew $command) : WarningEntity {
        try {
            $this->validator->assertUser($command->user);
            $this->validator->assertService($command->service);
            $this->validator->assertSlug($command->slug);

            if (isset($command->attribute)) {
                $this->validator->assertSlug($command->attribute);
            }
        } catch (ValidationException $e) {
            throw new Validate\Profile\WarningException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        $entity = $this->repository->create(
            [
                'user_id'    => $command->user->id,
                'creator'    => $command->service->id,
                'slug'       => $command->slug,
                'attribute'  => $command->attribute,
                'created_at' => time()
            ]
        );

        try {
            $entity = $this->repository->save($entity);
            $entity = $this->repository->hydrateRelations($entity);

            $event = $this->eventFactory->create('Profile\\Warning\\Created', $entity);
            $this->emitter->emit($event);
        } catch (\Exception $exception) {
            throw new Create\Profile\WarningException('Error while trying to create a warning', 500, $e);
        }

        return $entity;
    }

    /**
     * Deletes a Warning.
     *
     * @param \App\Command\Profile\Warning\DeleteOne $command
     *
     * @throws \App\Exception\Validate\WarningException
     * @throws \App\Exception\AppException
     *
     * @see \App\Repository\DBWarning::findOneBySlug
     * @see \App\Repository\DBWarning::delete
     *
     * @return void
     */
    public function handleDeleteOne(DeleteOne $command) : int {
        try {
            $this->validator->assertUser($command->user);
            $this->validator->assertService($command->service);
            $this->validator->assertSlug($command->slug);
        } catch (ValidationException $e) {
            throw new Validate\Profile\WarningException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        $entity = $this->repository->findOne($command->slug, $command->service->id, $command->user->id);

        try {
            $affectedRows = $this->repository->delete($entity->id);

            $event = $this->eventFactory->create('Profile\\Warning\\Deleted', $entity);
            $this->emitter->emit($event);
        } catch (\Exception $e) {
            throw new AppException('Error while deleting warning');
        }

        return $affectedRows;
    }

    /**
     * Deletes all settings ($command->userId).
     *
     * @param \App\Command\Profile\Warning\DeleteAll $command
     *
     * @throws \App\Exception\Validate\Profile\WarningException
     * @throws \App\Exception\AppException
     *
     * @see \App\Repository\DBWarning::findBy
     * @see \App\Repository\DBWarning::delete
     *
     * @return int
     */
    public function handleDeleteAll(DeleteAll $command) : int {
        try {
            $this->validator->assertUser($command->user);
            $this->validator->assertService($command->service);
        } catch (ValidationException $e) {
            throw new Validate\Profile\WarningException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        $entities = $this->repository->getByUserIdAndServiceId($command->service->id, $command->user->id, $command->queryParams);

        $affectedRows = 0;
        try {
            foreach ($entities as $entity) {
                $affectedRows += $this->repository->delete($entity->id);
            }

            $event = $this->eventFactory->create('Profile\\Warning\\DeletedMulti', $entities);
            $this->emitter->emit($event);
        } catch (\Exception $e) {
            throw new AppException('Error while deleting warnings');
        }

        return $affectedRows;
    }
}
