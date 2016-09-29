<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Handler\Profile;

use App\Command\Profile\Attribute\CreateNew;
use App\Command\Profile\Attribute\DeleteAll;
use App\Command\Profile\Attribute\DeleteOne;
use App\Command\Profile\Attribute\UpdateOne;
use App\Entity\Profile\Attribute as AttributeEntity;
use App\Exception\Create;
use App\Exception\NotFound;
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
     * @var App\Repository\Profile\AttributeInterface
     */
    private $repository;
    /**
     * Attribute Validator instance.
     *
     * @var App\Validator\Profile\Attribute
     */
    private $validator;
    /**
     * Event factory instance.
     *
     * @var App\Factory\Event
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
     * @param App\Repository\AttributeInterface $repository
     * @param App\Validator\Attribute           $validator
     * @param App\Factory\Event                 $eventFactory
     * @param \League\Event\Emitter             $emitter
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
     * Creates a new attribute data in the given user.
     *
     * @param App\Command\Profile\Attribute\CreateNew $command
     *
     * @see App\Repository\DBAttribute::save
     *
     * @throws App\Exception\Validade\AttributeExceptions
     * @throws App\Exception\Create\AttributeExceptions
     *
     * @return App\Entity\Attribute
     */
    public function handleCreateNew(CreateNew $command) : AttributeEntity {
        try {
            $this->validator->assertUser($command->user);
            $this->validator->assertService($command->service);
            $this->validator->assertLongName($command->name);
            $this->validator->assertValue($command->value);
            $this->validator->assertFloat($command->support);
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
            'creator'    => $command->service->id,
            'name'       => $command->name,
            'value'      => $command->value,
            'support'    => $command->support,
            'created_at' => time()
            ]
        );

        try {
            $entity = $this->repository->save($entity);
            $event  = $this->eventFactory->create('Profile\\Attribute\\Created', $entity);
            $this->emitter->emit($event);
        } catch (\Exception $e) {
            throw new Create\Profile\AttributeException('Error while trying to create an attribute', 500, $e);
        }

        return $entity;
    }

    /**
     * Updates a attribute data from a given user.
     *
     * @param App\Command\Profile\Attribute\UpdateOne $command
     *
     * @see App\Repository\DBAttribute::findOneByUserIdAndName
     * @see App\Repository\DBAttrubute::save
     *
     * @throws App\Exception\Validate\AttributeException
     * @throws App\Exception\Update\AttributeException
     *
     * @return App\Entity\Attribute
     */
    public function handleUpdateOne(UpdateOne $command) : AttributeEntity {
        try {
            $this->validator->assertValue($command->value);
        } catch (ValidationException $e) {
            throw new Validate\Profile\AttributeException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        try {
            $entity = $this->repository->save($entity);
            $entity = $this->repository->hydrateRelations($entity);

            $event = $this->eventFactory->create('Profile\\Attribute\\Created', $entity);
            $this->emitter->emit($event);
        } catch (\Exception $e) {
            throw new Update\Profile\AttributeException('Error while trying to update an attribute', 500, $e);
        }

        return $entity;
    }

    /**
     * Deletes a attribute data from a given user.
     *
     * @param App\Command\Profile\Attribute\DeleteOne $command
     *
     * @see App\Repository\DBAttribute::findOneByUserIdAndName
     * @see App\Repository\DBAttribute::deleteOneByUSerIdAndName
     *
     * @throws App\Exception\Validate\AttributeException
     * @throws App\Exception\NotFound\AttributeException
     *
     * @return int
     */
    public function handleDeleteOne(DeleteOne $command) : int {
        try {
            $this->validator->assertUser($command->user);
            $this->validator->assertService($command->service);
            $this->validator->assertLongName($command->name);
        } catch (ValidationException $e) {
            throw new Validate\Profile\AttributeException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

            $entities = $this->repository->findBy(
                [
                    'user_id' => $command->user->id,
                    'creator' => $command->service->id,
                    'name'    => $command->name
                ]
            );

        $affectedRows = 0;

        try {
            foreach ($entities as $entity) {
                $affectedRows += $this->repository->delete($entity->id);
            }

            $event = $this->eventFactory->create('Profile\\Attribute\\Deleted', $entities);
            $this->emitter->emit($event);
        } catch (\Exception $e) {
            throw new NotFound\Profile\AttributeException('No attributes found for deletion', 404);
        }

        return $affectedRows;
    }

    /**
     * Deletes all attribute data from a given user.
     *
     * @param App\Command\Profile\Attribute\DeleteAll $command
     *
     * @see App\Repository\DBAttribute::getAllByUserIdAndNames
     * @see App\Repository\DBAttribute::deleteByUserId
     *
     * @return int
     */
    public function handleDeleteAll(DeleteAll $command) : int {
        $this->validator->assertUser($command->user);
        $this->validator->assertService($command->service);
        $this->validator->assertArray($command->queryParams);

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

            $event = $this->eventFactory->create('Profile\\Attribute\\DeletedMulti', $entities);
            $this->emitter->emit($event);
        } catch (\Exception $e) {
            throw new NotFound\AttributeException('Error while deleting all attributes', 404);
        }

            return $affectedRows;
    }
}
