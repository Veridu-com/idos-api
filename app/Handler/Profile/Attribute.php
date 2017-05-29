<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Handler\Profile;

use App\Command\Profile\Attribute\CreateNew;
use App\Command\Profile\Attribute\DeleteAll;
use App\Command\Profile\Attribute\Upsert;
use App\Command\Profile\Attribute\UpsertBulk;
use App\Entity\Profile\Attribute as AttributeEntity;
use App\Exception\Create;
use App\Exception\NotFound;
use App\Exception\Upsert\Profile\AttributeException as UpsertException;
use App\Exception\Validate;
use App\Factory\Event;
use App\Handler\HandlerInterface;
use App\Repository\Profile\AttributeInterface;
use App\Validator\Profile\Attribute as AttributeValidator;
use Interop\Container\ContainerInterface;
use League\Event\Emitter;
use Respect\Validation\Exceptions\ValidationException;

/**
 * Handles Attribute commands.
 */
class Attribute implements HandlerInterface {
    /**
     * Attribute Repository instance.
     *
     * @var \App\Repository\Profile\AttributeInterface
     */
    private $repository;
    /**
     * Attribute Validator instance.
     *
     * @var \App\Validator\Profile\Attribute
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
            return new \App\Handler\Profile\Attribute(
                $container
                    ->get('repositoryFactory')
                    ->create('Profile\Attribute'),
                $container
                    ->get('validatorFactory')
                    ->create('Profile\Attribute'),
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
     * @param \App\Repository\Profile\AttributeInterface $repository
     * @param \App\Validator\Profile\Attribute           $validator
     * @param \App\Factory\Event                         $eventFactory
     * @param \League\Event\Emitter                      $emitter
     *
     * @return void
     */
    public function __construct(
        AttributeInterface $repository,
        AttributeValidator $validator,
        Event $eventFactory,
        Emitter $emitter
    ) {
        $this->repository   = $repository;
        $this->validator    = $validator;
        $this->eventFactory = $eventFactory;
        $this->emitter      = $emitter;
    }

    /**
     * Creates a new attribute data for the given user.
     *
     * @param \App\Command\Profile\Attribute\CreateNew $command
     *
     * @see \App\Repository\DBAttribute::save
     *
     * @throws \App\Exception\Validate\Profile\AttributeException
     * @throws \App\Exception\Create\Profile\AttributeException
     *
     * @return \App\Entity\Profile\Attribute
     */
    public function handleCreateNew(CreateNew $command) : AttributeEntity {
        try {
            $this->validator->assertUser($command->user);
            $this->validator->assertLongName($command->name);
            $this->validator->assertString($command->value);
            $this->validator->assertCredential($command->credential);
        } catch (ValidationException $e) {
            throw new Validate\Profile\AttributeException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        $entity = $this->repository->create(
            [
                'user_id'    => $command->user->id,
                'name'       => $command->name,
                'value'      => $command->value,
                'created_at' => time()
            ]
        );

        try {
            $entity = $this->repository->save($entity);
            $event  = $this->eventFactory->create('Profile\\Attribute\\Created', $entity, $command->credential);
            $this->emitter->emit($event);
        } catch (\Exception $e) {
            throw new Create\Profile\AttributeException('Error while trying to create an attribute', 500, $e);
        }

        return $entity;
    }

    /**
     * Creates or updates attribute data for the given user.
     *
     * @param \App\Command\Profile\Attribute\Upsert $command
     *
     * @see \App\Repository\DBAttribute::save
     *
     * @throws \App\Exception\Validate\Profile\AttributeException
     * @throws \App\Exception\Create\Profile\AttributeException
     *
     * @return \App\Entity\Profile\Attribute
     */
    public function handleUpsert(Upsert $command) : AttributeEntity {
        try {
            $this->validator->assertUser($command->user);
            $this->validator->assertLongName($command->name);
            $this->validator->assertString($command->value);
            $this->validator->assertCredential($command->credential);
        } catch (ValidationException $e) {
            throw new Validate\Profile\AttributeException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        $now    = date('Y-m-d H:i:s');
        $entity = $this->repository->create(
            [
                'user_id'    => $command->user->id,
                'name'       => $command->name,
                'value'      => $command->value,
                'created_at' => $now
            ]
        );

        $serialized = $entity->serialize();

        $this->repository->upsert(
            $entity,
            [
                'user_id',
                'name'
            ],
            [
                'value'      => $serialized['value'],
                'updated_at' => $now
            ]
        );

        $event = $this->eventFactory->create('Profile\\Attribute\\Created', $entity, $command->credential);
        $this->emitter->emit($event);

        return $entity;
    }

    /**
     * Creates or updates attribute bulk data for the given user.
     *
     * @param \App\Command\Profile\Attribute\UpsertBulk $command
     *
     * @see \App\Repository\DBAttribute::save
     *
     * @throws \App\Exception\Validate\Profile\AttributeException
     * @throws \App\Exception\Create\Profile\AttributeException
     *
     * @return array entities
     */
    public function handleUpsertBulk(UpsertBulk $command) : array {
        try {
            $this->validator->assertUser($command->user);
            $this->validator->assertAttributeArray($command->attributes);
            $this->validator->assertCredential($command->credential);
        } catch (ValidationException $e) {
            throw new Validate\Profile\AttributeException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        $entities = [];
        foreach ($command->attributes as $attribute) {
            $entities[] = $this->repository->create($attribute);
        }

        try {
            $this->repository->beginTransaction();
            $now = date('Y-m-d H:i:s');

            foreach ($entities as $entity) {
                $serialized = $entity->serialize();

                $this->repository->upsert(
                    $entity,
                    [
                        'user_id',
                        'name'
                    ],
                    [
                        'value'      => $serialized['value'],
                        'updated_at' => $now
                    ]
                );
            }

            $event = $this->eventFactory->create('Profile\\Attribute\\UpsertedBulk', $command->attributes, $command->user, $command->credential);
            $this->emitter->emit($event);

            $this->repository->commit();
        } catch (\Exception $e) {
            $this->repository->rollBack();
            throw new UpsertException('Error while upserting attributes.');
        }

        return $entities;
    }

    /**
     * Deletes all attribute data from a given user.
     *
     * @param \App\Command\Profile\Attribute\DeleteAll $command
     *
     * @see \App\Repository\DBAttribute::getAllByUserIdAndNames
     * @see \App\Repository\DBAttribute::deleteByUserId
     *
     * @return int
     */
    public function handleDeleteAll(DeleteAll $command) : int {
        try {
            $this->validator->assertUser($command->user);
            $this->validator->assertArray($command->queryParams);
            $this->validator->assertCredential($command->credential);
        } catch (ValidationException $e) {
            throw new Validate\Profile\AttributeException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        $entities = $this->repository->findByUserId($command->user->id, $command->queryParams);

        $affectedRows = 0;

        try {
            $affectedRows = $this->repository->deleteByUserId($command->user->id);

            if ($affectedRows) {
                $event = $this->eventFactory->create('Profile\\Attribute\\DeletedMulti', $entities, $command->credential);
                $this->emitter->emit($event);
            }
        } catch (\Exception $e) {
            throw new NotFound\Profile\AttributeException('Error while deleting all attributes', 404);
        }

        return $affectedRows;
    }
}
