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
use App\Repository\ReferenceInterface;
use App\Validator\Reference as ReferenceValidator;
use Interop\Container\ContainerInterface;
use League\Event\Emitter;

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
     * @return App\Entity\Reference
     */
    public function handleCreateNew(CreateNew $command) : ReferenceEntity {
        $this->validator->assertName($command->name);
        $this->validator->assertValue($command->value);

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
            throw new AppException('Error while creating an reference');
        }

        return $reference;
    }

    /**
     * Updates a reference data from a given user.
     *
     * @param App\Command\Reference\UpdateOne $command
     *
     * @return App\Entity\Reference
     */
    public function handleUpdateOne(UpdateOne $command) : ReferenceEntity {
        $this->validator->assertValue($command->value);

        $reference        = $this->repository->findOneByUserIdAndName($command->user->id, $command->name);
        $reference->value = $command->value;

        try {
            $reference = $this->repository->save($reference);
            $event     = new Updated($reference);
            $this->emitter->emit($event);
        } catch (\Exception $e) {
            throw new AppException('Error while updating reference');
        }

        return $reference;
    }

    /**
     * Deletes a reference data from a given user.
     *
     * @param App\Command\Reference\DeleteOne $command
     *
     * @return int
     */
    public function handleDeleteOne(DeleteOne $command) : int {
        $this->validator->assertName($command->name);

        $reference = $this->repository->findOneByUserIdAndName($command->user->id, $command->name);

        try {
            $affectedRows = $this->repository->deleteOneByUserIdAndName($command->user->id, $command->name);
            $event        = new Deleted($reference);
            $this->emitter->emit($event);
        } catch (\Exception $e) {
            throw new AppException('Error while deleting reference');
        }

        return $affectedRows;
    }

    /**
     * Deletes all reference data from a given user.
     *
     * @param App\Command\Reference\DeleteAll $command
     *
     * @return int
     */
    public function handleDeleteAll(DeleteAll $command) : int {
        $references = $this->repository->getAllByUserId($command->user->id);

        try {
            $affectedRows = $this->repository->deleteByUserId($command->user->id);
            $event        = new DeletedMulti($references);
            $this->emitter->emit($event);
        } catch (\Exception $e) {
            throw new AppException('Error while deleting references');
        }

        return $affectedRows;
    }
}
