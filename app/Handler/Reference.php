<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Handler;

use App\Command\Reference\CreateNew;
use App\Command\Reference\DeleteAll;
use App\Command\Reference\DeleteOne;
use App\Command\Reference\UpdateOne;
use App\Entity\Reference as ReferenceEntity;
use App\Event\Reference\Created;
use App\Event\Reference\Deleted;
use App\Event\Reference\DeletedMulti;
use App\Event\Reference\Updated;
use App\Exception\Create;
use App\Exception\Update;
use App\Exception\Validate;
use App\Repository\ReferenceInterface;
use App\Validator\Reference as ReferenceValidator;
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
     * @var App\Repository\ReferenceInterface
     */
    protected $repository;
    /**
     * Reference Validator instance.
     *
     * @var App\Validator\Reference
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
            return new \App\Handler\Reference(
                $container
                    ->get('repositoryFactory')
                    ->create('Reference'),
                $container
                    ->get('validatorFactory')
                    ->create('Reference'),
                $container
                    ->get('eventEmitter')
            );
        };
    }

    /**
     * Class constructor.
     *
     * @param App\Repository\ReferenceInterface $repository
     * @param App\Validator\Reference           $validator
     *
     * @return void
     */
    public function __construct(
        ReferenceInterface $repository,
        ReferenceValidator $validator,
        Emitter $emitter
    ) {
        $this->repository = $repository;
        $this->validator  = $validator;
        $this->emitter    = $emitter;
    }

    /**
     * Creates a new reference data in the given user.
     *
     * @param App\Command\Reference\CreateNew $command
     *
     * @see App\Repository\DBReference::create
     * @see App\Repository\DBReference::save
     *
     * @throws App\Exception\Validate\ReferenceException
     * @throws App\Exception\Create\ReferenceException
     *
     * @return App\Entity\Reference
     */
    public function handleCreateNew(CreateNew $command) : ReferenceEntity {
        try {
            $this->validator->assertName($command->name);
            $this->validator->assertValue($command->value);
        } catch (ValidationException $e) {
            throw new Validate\ReferenceException(
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
            'created_at' => time()
            ]
        );

        try {
            $reference = $this->repository->save($reference);
            $event     = new Created($reference);
            $this->emitter->emit($event);
        } catch (\Exception $e) {
            throw new Create\ReferenceException('Error while trying to create a reference', 500, $e);
        }

        return $reference;
    }

    /**
     * Updates a reference data from a given user.
     *
     * @param App\Command\Reference\UpdateOne $command
     *
     * @see App\Repository\DBReference::findOneByUserIdAndName
     * @see App\Repository\DBReference::save
     *
     * @throws App\Exception\Validate\ReferenceException
     * @throws App\Exception\Update\ReferenceException
     *
     * @return App\Entity\Reference
     */
    public function handleUpdateOne(UpdateOne $command) : ReferenceEntity {
        try {
            $this->validator->assertValue($command->value);
        } catch (ValidationException $e) {
            throw new Validate\ReferenceException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        $reference        = $this->repository->findOneByUserIdAndName($command->user->id, $command->name);
        $reference->value = $command->value;

        try {
            $reference = $this->repository->save($reference);
            $event     = new Updated($reference);
            $this->emitter->emit($event);
        } catch (\Exception $e) {
            throw new Update\ReferenceException('Error while trying to update a feature', 500, $e);
        }

        return $reference;
    }

    /**
     * Deletes a reference data from a given user.
     *
     * @param App\Command\Reference\DeleteOne $command
     *
     * @see App\Repository\DBReference::findOneByUserIdAndName
     * @see App\Repository\DBReference::deleteOneByUserIdAndName
     *
     * @throws App\Exception\Validate\RerefenceException
     * @throws App\Exception\NotFound\RerefenceException
     *
     * @return void
     */
    public function handleDeleteOne(DeleteOne $command) {
        try {
            $this->validator->assertName($command->name);
        } catch (ValidationException $e) {
            throw new Validate\ReferenceException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        $reference = $this->repository->findOneByUserIdAndName($command->user->id, $command->name);

        $affectedRows = $this->repository->deleteOneByUserIdAndName($command->user->id, $command->name);

        if (! $affectedRows) {
            throw new NotFound\ReferenceException('No references found for deletion', 404);
        }

        $event = new Deleted($reference);
        $this->emitter->emit($event);
    }

    /**
     * Deletes all reference data from a given user.
     *
     * @param App\Command\Reference\DeleteAll $command
     *
     * @see App\Repository\DBReference::getAllByUserId
     * @see App\Repository\DBReference::deleteByUserId
     *
     * @return int
     */
    public function handleDeleteAll(DeleteAll $command) : int {
        $references = $this->repository->getAllByUserId($command->user->id);

        $affectedRows = $this->repository->deleteByUserId($command->user->id);
        $event        = new DeletedMulti($references);
        $this->emitter->emit($event);

        return $affectedRows;
    }
}
