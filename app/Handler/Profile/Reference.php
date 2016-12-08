<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Handler\Profile;

use App\Command\Profile\Reference\CreateNew;
use App\Command\Profile\Reference\DeleteAll;
use App\Command\Profile\Reference\DeleteOne;
use App\Command\Profile\Reference\UpdateOne;
use App\Entity\Profile\Reference as ReferenceEntity;
use App\Exception\Create;
use App\Exception\Update;
use App\Exception\Validate;
use App\Exception\NotFound;
use App\Factory\Event;
use App\Handler\HandlerInterface;
use App\Repository\Profile\ReferenceInterface;
use App\Validator\Profile\Reference as ReferenceValidator;
use Interop\Container\ContainerInterface;
use League\Event\Emitter;
use Respect\Validation\Exceptions\ValidationException;

/**
 * Handles Reference commands.
 */
class Reference implements HandlerInterface {
    /**
     * Reference Repository instance.
     *
     * @var \App\Repository\Profile\ReferenceInterface
     */
    private $repository;
    /**
     * Reference Validator instance.
     *
     * @var \App\Validator\Profile\Reference
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
            return new \App\Handler\Profile\Reference(
                $container
                    ->get('repositoryFactory')
                    ->create('Profile\Reference'),
                $container
                    ->get('validatorFactory')
                    ->create('Profile\Reference'),
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
     * @param \App\Repository\Profile\ReferenceInterface $repository
     * @param \App\Validator\Profile\Reference           $validator
     * @param \App\Factory\Event                         $eventFactory
     * @param \League\Event\Emitter                      $emitter
     *
     * @return void
     */
    public function __construct(
        ReferenceInterface $repository,
        ReferenceValidator $validator,
        Event $eventFactory,
        Emitter $emitter
    ) {
        $this->repository   = $repository;
        $this->validator    = $validator;
        $this->eventFactory = $eventFactory;
        $this->emitter      = $emitter;
    }

    /**
     * Creates a new reference data in the given user.
     *
     * @param \App\Command\Profile\Reference\CreateNew $command
     *
     * @see \App\Repository\DBReference::create
     * @see \App\Repository\DBReference::save
     *
     * @throws \App\Exception\Validate\Profile\ReferenceException
     * @throws \App\Exception\Create\Profile\ReferenceException
     *
     * @return \App\Entity\Profile\Reference
     */
    public function handleCreateNew(CreateNew $command) : ReferenceEntity {
        try {
            $this->validator->assertName($command->name);
            $this->validator->assertValue($command->value);
            $this->validator->assertCredential($command->credential);
        } catch (ValidationException $e) {
            throw new Validate\Profile\ReferenceException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        $reference = $this->repository->create(
            [
            'user_id'    => $command->user->id,
            'name'       => $command->name,
            'value'      => $command->value,
            'ipaddr'     => $command->ipaddr,
            'created_at' => time()
            ]
        );

        try {
            $reference = $this->repository->save($reference);

            $event = $this->eventFactory->create('Profile\\Reference\\Created', $reference, $command->credential);
            $this->emitter->emit($event);
        } catch (\Exception $e) {
            throw new Create\Profile\ReferenceException('Error while trying to create a reference', 500, $e);
        }

        return $reference;
    }

    /**
     * Updates a reference data from a given user.
     *
     * @param \App\Command\Profile\Reference\UpdateOne $command
     *
     * @see \App\Repository\DBReference::findOneByUserIdAndName
     * @see \App\Repository\DBReference::save
     *
     * @throws \App\Exception\Validate\Profile\ReferenceException
     * @throws \App\Exception\Update\Profile\ReferenceException
     *
     * @return \App\Entity\Profile\Reference
     */
    public function handleUpdateOne(UpdateOne $command) : ReferenceEntity {
        try {
            $this->validator->assertValue($command->value);
            $this->validator->assertCredential($command->credential);
        } catch (ValidationException $e) {
            throw new Validate\Profile\ReferenceException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        $reference        = $this->repository->findOne($command->name, $command->user->id);
        $reference->value = $command->value;

        try {
            $reference = $this->repository->save($reference);

            $event = $this->eventFactory->create('Profile\\Reference\\Updated', $reference, $command->credential);
            $this->emitter->emit($event);
        } catch (\Exception $e) {
            throw new Update\Profile\ReferenceException('Error while trying to update a feature', 500, $e);
        }

        return $reference;
    }

    /**
     * Deletes a reference data from a given user.
     *
     * @param \App\Command\Profile\Reference\DeleteOne $command
     *
     * @see \App\Repository\DBReference::findOneByUserIdAndName
     * @see \App\Repository\DBReference::deleteOneByUserIdAndName
     *
     * @throws \App\Exception\Validate\Profile\ReferenceException
     * @throws \App\Exception\NotFound\Profile\ReferenceException
     *
     * @return void
     */
    public function handleDeleteOne(DeleteOne $command) {
        try {
            $this->validator->assertName($command->name);
            $this->validator->assertCredential($command->credential);
        } catch (ValidationException $e) {
            throw new Validate\Profile\ReferenceException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        $reference = $this->repository->findOne($command->name, $command->user->id);

        $affectedRows = $this->repository->deleteOne($command->name, $command->user->id);
        if (! $affectedRows) {
            throw new NotFound\Profile\ReferenceException('No references found for deletion', 404);
        }

        $event = $this->eventFactory->create('Profile\\Reference\\Deleted', $reference, $command->credential);
        $this->emitter->emit($event);
    }

    /**
     * Deletes all reference data from a given user.
     *
     * @param \App\Command\Profile\Reference\DeleteAll $command
     *
     * @see \App\Repository\DBReference::getAllByUserId
     * @see \App\Repository\DBReference::deleteByUserId
     *
     * @return int
     */
    public function handleDeleteAll(DeleteAll $command) : int {
        $references   = $this->repository->getAllByUserId($command->user->id);
        $affectedRows = $this->repository->deleteByUserId($command->user->id);

        $event = $this->eventFactory->create('Profile\\Reference\\DeletedMulti', $references, $command->credential);
        $this->emitter->emit($event);

        return $affectedRows;
    }
}
