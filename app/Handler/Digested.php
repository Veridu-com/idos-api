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
use App\Repository\DigestedInterface;
use App\Validator\Digested as DigestedValidator;
use Interop\Container\ContainerInterface;

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
                    ->create('Digested')
            );
        };
    }

    /**
     * Class constructor.
     *
     * @param App\Repository\DigestedInterface $repository
     * @param App\Validator\Digested           $validator
     *
     * @return void
     */
    public function __construct(
        DigestedInterface $repository,
        DigestedValidator $validator
    ) {
        $this->repository = $repository;
        $this->validator  = $validator;
    }

    /**
     * Creates a new digested data in the given source.
     *
     * @param App\Command\Digested\CreateNew $command
     *
     * @return App\Entity\Digested
     */
    public function handleCreateNew(CreateNew $command) : DigestedEntity {
        $this->validator->assertName($command->name);
        $this->validator->assertValue($command->value);

        //@FIXME: check here if given source ($command->sourceId) has user_id == $command->user->id

        $digested = $this->repository->create(
            [
            'source_id'  => $command->sourceId,
            'name'       => $command->name,
            'value'      => $command->value,
            'created_at' => time()
            ]
        );

        $digested = $this->repository->save($digested);

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
        $this->validator->assertValue($command->value);

        //@FIXME: check here if given source ($command->sourceId) has user_id == $command->user->id

        $digested        = $this->repository->findOneByUserIdSourceIdAndName($command->user->id, $command->sourceId, $command->name);
        $digested->value = $command->value;
        $digested        = $this->repository->save($digested);

        return $digested;
    }

    /**
     * Deletes a digested data from a given source.
     *
     * @param App\Command\Digested\DeleteOne $command
     *
     * @return int
     */
    public function handleDeleteOne(DeleteOne $command) : int {
        $this->validator->assertName($command->name);

        //@FIXME: check here if given source ($command->sourceId) has user_id == $command->user->id

        return $this->repository->deleteOneBySourceIdAndName($command->sourceId, $command->name);
    }

    /**
     * Deletes all digested data from a given source.
     *
     * @param App\Command\Digested\DeleteAll $command
     *
     * @return int
     */
    public function handleDeleteAll(DeleteAll $command) : int {
        //@FIXME: check here if given source ($command->sourceId) has user_id == $command->user->id

        return $this->repository->deleteBySourceId($command->sourceId);
    }
}
