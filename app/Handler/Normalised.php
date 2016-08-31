<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Handler;

use App\Command\Normalised\CreateNew;
use App\Command\Normalised\DeleteAll;
use App\Command\Normalised\DeleteOne;
use App\Command\Normalised\UpdateOne;
use App\Entity\Normalised as NormalisedEntity;
use App\Repository\NormalisedInterface;
use App\Validator\Normalised as NormalisedValidator;
use Interop\Container\ContainerInterface;

/**
 * Handles Normalised commands.
 */
class Normalised implements HandlerInterface {
    /**
     * Normalised Repository instance.
     *
     * @var App\Repository\NormalisedInterface
     */
    protected $repository;
    /**
     * Normalised Validator instance.
     *
     * @var App\Validator\Normalised
     */
    protected $validator;

    /**
     * {@inheritdoc}
     */
    public static function register(ContainerInterface $container) {
        $container[self::class] = function (ContainerInterface $container) {
            return new \App\Handler\Normalised(
                $container
                    ->get('repositoryFactory')
                    ->create('Normalised'),
                $container
                    ->get('validatorFactory')
                    ->create('Normalised')
            );
        };
    }

    /**
     * Class constructor.
     *
     * @param App\Repository\NormalisedInterface $repository
     * @param App\Validator\Normalised           $validator
     *
     * @return void
     */
    public function __construct(
        NormalisedInterface $repository,
        NormalisedValidator $validator
    ) {
        $this->repository = $repository;
        $this->validator  = $validator;
    }

    /**
     * Creates a new normalised data in the given source.
     *
     * @param App\Command\Normalised\CreateNew $command
     *
     * @return App\Entity\Normalised
     */
    public function handleCreateNew(CreateNew $command) : NormalisedEntity {
        $this->validator->assertName($command->name);
        $this->validator->assertValue($command->value);

        //@FIXME: check here if given source ($command->sourceId) has user_id == $command->user->id

        $normalised = $this->repository->create(
            [
                'source_id'  => $command->sourceId,
                'name'       => $command->name,
                'value'      => $command->value,
                'created_at' => time()
            ]
        );

        $normalised = $this->repository->save($normalised);

        return $normalised;
    }

    /**
     * Updates a normalised data from a given source.
     *
     * @param App\Command\Normalised\UpdateOne $command
     *
     * @return App\Entity\Normalised
     */
    public function handleUpdateOne(UpdateOne $command) : NormalisedEntity {
        $this->validator->assertValue($command->value);

        //@FIXME: check here if given source ($command->sourceId) has user_id == $command->user->id

        $normalised        = $this->repository->findOneByUserIdSourceIdAndName($command->user->id, $command->sourceId, $command->name);
        $normalised->value = $command->value;
        $normalised        = $this->repository->save($normalised);

        return $normalised;
    }

    /**
     * Deletes a normalised data from a given source.
     *
     * @param App\Command\Normalised\DeleteOne $command
     *
     * @return int
     */
    public function handleDeleteOne(DeleteOne $command) : int {
        $this->validator->assertName($command->name);

        //@FIXME: check here if given source ($command->sourceId) has user_id == $command->user->id

        return $this->repository->deleteOneBySourceIdAndName($command->sourceId, $command->name);
    }

    /**
     * Deletes all normalised data from a given source.
     *
     * @param App\Command\Normalised\DeleteAll $command
     *
     * @return int
     */
    public function handleDeleteAll(DeleteAll $command) : int {
        //@FIXME: check here if given source ($command->sourceId) has user_id == $command->user->id

        return $this->repository->deleteBySourceId($command->sourceId);
    }
}
