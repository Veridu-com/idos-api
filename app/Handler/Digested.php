<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Handler;

use App\Command\Digested\CreateNew;
use App\Command\Digested\DeleteAll;
use App\Command\Digested\DeleteOne;
use App\Command\Digested\UpdateOne;
use App\Entity\Digested as DigestedEntity;
use App\Event\Digested\Created;
use App\Event\Digested\Deleted;
use App\Event\Digested\DeletedMulti;
use App\Event\Digested\Updated;
use App\Exception\Create;
use App\Exception\NotFound;
use App\Exception\Update;
use App\Exception\Validate;
use App\Repository\DigestedInterface;
use App\Validator\Digested as DigestedValidator;
use Interop\Container\ContainerInterface;
use League\Event\Emitter;
use Respect\Validation\Exceptions\ValidationException;

/**
 * Handles Digested commands.
 */
class Digested implements HandlerInterface {
    /**
     * Digested Repository instance.
     *
     * @var App\Repository\DigestedInterface
     */
    protected $repository;
    /**
     * Digested Validator instance.
     *
     * @var App\Validator\Digested
     */
    protected $validator;
    /**
     * Event emitter instance.
     *
     * @var \League\Event\Emitter
     */
    protected $emitter;

    /**
     * {@inheritdoc}
     */
    public static function register(ContainerInterface $container) {
        $container[self::class] = function (ContainerInterface $container) {
            return new \App\Handler\Digested(
                $container
                    ->get('repositoryFactory')
                    ->create('Digested'),
                $container
                    ->get('validatorFactory')
                    ->create('Digested'),
                $container
                    ->get('eventEmitter')
            );
        };
    }

    /**
     * Class constructor.
     *
     * @param App\Repository\DigestedInterface $repository
     * @param App\Validator\Digested           $validator
     * @param \League\Event\Emitter            $emitter
     *
     * @return void
     */
    public function __construct(
        DigestedInterface $repository,
        DigestedValidator $validator,
        Emitter $emitter
    ) {
        $this->repository = $repository;
        $this->validator  = $validator;
        $this->emitter    = $emitter;
    }

    /**
     * Creates a new digested data in the given source.
     *
     * @param App\Command\Digested\CreateNew $command
     *
     * @return App\Entity\Digested
     */
    public function handleCreateNew(CreateNew $command) : DigestedEntity {
        try {
            $this->validator->assertId($command->sourceId);
            $this->validator->assertLongName($command->name);
            $this->validator->assertValue($command->value);
        } catch (ValidationException $e) {
            throw new Validate\DigestedException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        //@FIXME: check here if given source ($command->sourceId) has user_id == $command->user->id

        $digested = $this->repository->create(
            [
            'source_id'  => $command->sourceId,
            'name'       => $command->name,
            'value'      => $command->value,
            'created_at' => time()
            ]
        );

        try {
            $digested = $this->repository->save($digested);
            $event    = new Created($digested);
            $this->emitter->emit($event);
        } catch (\Exception $e) {
            throw new Create\DigestedException('Error while trying to create a digested', 500, $e);
        }

        return $digested;
    }

    /**
     * Updates a digested data from a given source.
     *
     * @param App\Command\Digested\UpdateOne $command
     *
     * @return App\Entity\Digested
     */
    public function handleUpdateOne(UpdateOne $command) : DigestedEntity {
        try {
            $this->validator->assertUser($command->user);
            $this->validator->assertId($command->user->id);
            $this->validator->assertValue($command->value);
            $this->validator->assertId($command->sourceId);
        } catch (ValidationException $e) {
            throw new Validate\DigestedException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        //@FIXME: check here if given source ($command->sourceId) has user_id == $command->user->id

        $digested = $this->repository->findOneByUserIdSourceIdAndName(
            $command->user->id,
            $command->sourceId,
            $command->name
        );
        $digested->value = $command->value;

        try {
            $digested = $this->repository->save($digested);
            $event    = new Updated($digested);
            $this->emitter->emit($event);
        } catch (\Exception $e) {
            throw new Update\DigestedException('Error while trying to update a digested', 500, $e);
        }

        return $digested;
    }

    /**
     * Deletes a digested data from a given source.
     *
     * @param App\Command\Digested\DeleteOne $command
     *
     * @return void
     */
    public function handleDeleteOne(DeleteOne $command) {
        try {
            $this->validator->assertUser($command->user);
            $this->validator->assertId($command->user->id);
            $this->validator->assertId($command->sourceId);
            $this->validator->assertLongName($command->name);
        } catch (\Exception $e) {
            throw new Validate\DigestedException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        //@FIXME: check here if given source ($command->sourceId) has user_id == $command->user->id
        $digested = $this->repository->findOneByUserIdSourceIdAndName(
            $command->user->id,
            $command->sourceId,
            $command->name
        );

        $rowsAffected = $this->repository->deleteOneBySourceIdAndName($command->sourceId, $command->name);

        if (! $rowsAffected) {
            throw new NotFound\DigestedException('No digesteds found for deletion', 404);
        }

        $event = new Deleted($digested);
        $this->emitter->emit($event);
    }

    /**
     * Deletes all digested data from a given source.
     *
     * @param App\Command\Digested\DeleteAll $command
     *
     * @return int
     */
    public function handleDeleteAll(DeleteAll $command) : int {
        try {
            $this->validator->assertUser($command->user);
            $this->validator->assertId($command->user->id);
            $this->validator->assertId($command->sourceId);
        } catch (\Exception $e) {
            throw new Validate\DigestedException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        //@FIXME: check here if given source ($command->sourceId) has user_id == $command->user->id

        $deletedItems = $this->repository->getAllByUserIdAndSourceId($command->user->id, $command->sourceId);

        $rowsAffected = $this->repository->deleteBySourceId($command->sourceId);

        $event = new DeletedMulti($deletedItems);
        $this->emitter->emit($event);

        return $rowsAffected;
    }
}
