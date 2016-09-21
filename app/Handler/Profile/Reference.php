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
use App\Event\Profile\Reference\Created;
use App\Event\Profile\Reference\Deleted;
use App\Event\Profile\Reference\DeletedMulti;
use App\Event\Profile\Reference\Updated;
use App\Exception\Create;
use App\Exception\Update;
use App\Exception\Validate;
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
     * @var App\Repository\Profile\ReferenceInterface
     */
    protected $repository;
    /**
     * Reference Validator instance.
     *
     * @var App\Validator\Profile\Reference
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
            return new \App\Handler\Profile\Reference(
                $container
                    ->get('repositoryFactory')
                    ->create('Profile\Reference'),
                $container
                    ->get('validatorFactory')
                    ->create('Profile\Reference'),
                $container
                    ->get('eventEmitter')
            );
        };
    }

    /**
     * Class constructor.
     *
     * @param App\Repository\Profile\ReferenceInterface $repository
     * @param App\Validator\Profile\Reference           $validator
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
     * @param App\Command\Profile\Reference\CreateNew $command
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
            'created_at' => time()
            ]
        );

        try {
            $reference = $this->repository->save($reference);
            $event     = new Created($reference);
            $this->emitter->emit($event);
        } catch (\Exception $e) {
            throw new Create\Profile\ReferenceException('Error while trying to create a reference', 500, $e);
        }

        return $reference;
    }

    /**
     * Updates a reference data from a given user.
     *
     * @param App\Command\Profile\Reference\UpdateOne $command
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
            throw new Validate\Profile\ReferenceException(
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
            throw new Update\Profile\ReferenceException('Error while trying to update a feature', 500, $e);
        }

        return $reference;
    }

    /**
     * Deletes a reference data from a given user.
     *
     * @param App\Command\Profile\Reference\DeleteOne $command
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
            throw new Validate\Profile\ReferenceException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        $reference = $this->repository->findOneByUserIdAndName($command->user->id, $command->name);

        $affectedRows = $this->repository->deleteOneByUserIdAndName($command->user->id, $command->name);

        if (! $affectedRows) {
            throw new NotFound\Profile\ReferenceException('No references found for deletion', 404);
        }

        $event = new Deleted($reference);
        $this->emitter->emit($event);
    }

    /**
     * Deletes all reference data from a given user.
     *
     * @param App\Command\Profile\Reference\DeleteAll $command
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
