<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Handler;

use App\Command\Raw\CreateNew;
use App\Command\Raw\DeleteAll;
use App\Command\Raw\DeleteOne;
use App\Command\Raw\UpdateOne;
use App\Entity\Raw as RawEntity;
use App\Repository\RawInterface;
use App\Validator\Raw as RawValidator;
use Interop\Container\ContainerInterface;

/**
 * Handles Raw commands.
 */
class Raw implements HandlerInterface {
    /**
     * Raw Repository instance.
     *
     * @var App\Repository\RawInterface
     */
    protected $repository;
    /**
     * Raw Validator instance.
     *
     * @var App\Validator\Raw
     */
    protected $validator;

    /**
     * {@inheritdoc}
     */
    public static function register(ContainerInterface $container) {
        $container[self::class] = function (ContainerInterface $container) {
            return new \App\Handler\Raw(
                $container
                    ->get('repositoryFactory')
                    ->create('Raw'),
                $container
                    ->get('validatorFactory')
                    ->create('Raw')
            );
        };
    }

    /**
     * Class constructor.
     *
     * @param App\Repository\RawInterface $repository
     * @param App\Validator\Raw           $validator
     *
     * @return void
     */
    public function __construct(
        RawInterface $repository,
        RawValidator $validator
    ) {
        $this->repository = $repository;
        $this->validator  = $validator;
    }

    /**
     * Creates a new raw data in the given source.
     *
     * @param App\Command\Raw\CreateNew $command
     *
     * @return App\Entity\Raw
     */
    public function handleCreateNew(CreateNew $command) : RawEntity {
        $this->validator->assertSource($command->source);
        $this->validator->assertBelongsToUser($command->source, $command->user);
        $this->validator->assertName($command->name);

        $raw = $this->repository->create([
            'source'     => $command->source,
            'name'       => $command->name,
            'data'       => $command->data,
            'created_at' => time()
        ]);

        $raw = $this->repository->save($raw);

        return $raw;
    }

    /**
     * Updates a raw data from a given source.
     *
     * @param App\Command\Raw\UpdateOne $command
     *
     * @return App\Entity\Raw
     */
    public function handleUpdateOne(UpdateOne $command) : RawEntity {
        $this->validator->assertSource($command->source);
        $this->validator->assertBelongsToUser($command->source, $command->user);
        $this->validator->assertName($command->name);

        return $this->repository->updateOneBySourceAndName($command->source, $command->name, $command->data);
    }

    /**
     * Deletes a raw data from a given source.
     *
     * @param App\Command\Raw\DeleteOne $command
     *
     * @return int
     */
    public function handleDeleteOne(DeleteOne $command) : int {
        $this->validator->assertSource($command->source);
        $this->validator->assertBelongsToUser($command->source, $command->user);
        $this->validator->assertName($command->name);

        return $this->repository->deleteOneBySourceAndName($command->source, $command->name);
    }

    /**
     * Deletes all raw data from a given source.
     *
     * @param App\Command\Raw\DeleteAll $command
     *
     * @return int
     */
    public function handleDeleteAll(DeleteAll $command) : int {
        $this->validator->assertSource($command->source);
        $this->validator->assertBelongsToUser($command->source, $command->user);

        return $this->repository->deleteBySource($command->source);
    }
}
