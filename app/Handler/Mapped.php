<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types=1);

namespace App\Handler;

use App\Command\Mapped\CreateNew;
use App\Command\Mapped\DeleteAll;
use App\Command\Mapped\DeleteOne;
use App\Command\Mapped\UpdateOne;
use App\Entity\Mapped as MappedEntity;
use App\Repository\MappedInterface;
use App\Validator\Mapped as MappedValidator;
use Interop\Container\ContainerInterface;

/**
 * Handles Mapped commands.
 */
class Mapped implements HandlerInterface {
    /**
     * Mapped Repository instance.
     *
     * @var App\Repository\MappedInterface
     */
    protected $repository;
    /**
     * Mapped Validator instance.
     *
     * @var App\Validator\Mapped
     */
    protected $validator;

    /**
     * {@inheritdoc}
     */
    public static function register(ContainerInterface $container) {
        $container[self::class] = function (ContainerInterface $container) {
            return new \App\Handler\Mapped(
                $container
                    ->get('repositoryFactory')
                    ->create('Mapped'),
                $container
                    ->get('validatorFactory')
                    ->create('Mapped')
            );
        };
    }

    /**
     * Class constructor.
     *
     * @param App\Repository\MappedInterface $repository
     * @param App\Validator\Mapped           $validator
     *
     * @return void
     */
    public function __construct(
        MappedInterface $repository,
        MappedValidator $validator
    ) {
        $this->repository = $repository;
        $this->validator  = $validator;
    }

    /**
     * Creates a new mapped data in the given source.
     *
     * @param App\Command\Mapped\CreateNew $command
     *
     * @return App\Entity\Mapped
     */
    public function handleCreateNew(CreateNew $command) : MappedEntity {
        $this->validator->assertName($command->name);
        $this->validator->assertValue($command->value);

        //@FIXME: check here if given source ($command->sourceId) has user_id == $command->user->id

        $mapped = $this->repository->create([
            'source_id'  => $command->sourceId,
            'name'       => $command->name,
            'value'      => $command->value,
            'created_at' => time()
        ]);

        $mapped = $this->repository->save($mapped);

        return $mapped;
    }

    /**
     * Updates a mapped data from a given source.
     *
     * @param App\Command\Mapped\UpdateOne $command
     *
     * @return App\Entity\Mapped
     */
    public function handleUpdateOne(UpdateOne $command) : MappedEntity {
        $this->validator->assertValue($command->value);

        //@FIXME: check here if given source ($command->sourceId) has user_id == $command->user->id

        $mapped        = $this->repository->findOneByUserIdSourceIdAndName($command->user->id, $command->sourceId, $command->name);
        $mapped->value = $command->value;
        $mapped        = $this->repository->save($mapped);

        return $mapped;
    }

    /**
     * Deletes a mapped data from a given source.
     *
     * @param App\Command\Mapped\DeleteOne $command
     *
     * @return int
     */
    public function handleDeleteOne(DeleteOne $command) : int {
        $this->validator->assertName($command->name);

        //@FIXME: check here if given source ($command->sourceId) has user_id == $command->user->id

        return $this->repository->deleteOneBySourceIdAndName($command->sourceId, $command->name);
    }

    /**
     * Deletes all mapped data from a given source.
     *
     * @param App\Command\Mapped\DeleteAll $command
     *
     * @return int
     */
    public function handleDeleteAll(DeleteAll $command) : int {
        //@FIXME: check here if given source ($command->sourceId) has user_id == $command->user->id

        return $this->repository->deleteBySourceId($command->sourceId);
    }

}
