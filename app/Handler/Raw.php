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
use App\Event\Raw\Created;
use App\Event\Raw\Deleted;
use App\Event\Raw\DeletedMulti;
use App\Event\Raw\Updated;
use App\Repository\RawInterface;
use App\Validator\Raw as RawValidator;
use Interop\Container\ContainerInterface;
use League\Event\Emitter;

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
            return new \App\Handler\Raw(
                $container
                    ->get('repositoryFactory')
                    ->create('Raw'),
                $container
                    ->get('validatorFactory')
                    ->create('Raw'),
                $container
                    ->get('eventEmitter')
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
        RawValidator $validator,
        Emitter $emitter
    ) {
        $this->repository = $repository;
        $this->validator  = $validator;
        $this->emitter    = $emitter;
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
        $this->validator->assertName($command->collection);

        $raw = $this->repository->create([
            'source'     => $command->source,
            'collection' => $command->collection,
            'data'       => $command->data,
            'created_at' => time()
        ]);

        try {
            $raw   = $this->repository->save($raw);
            $event = new Created($raw);
            $this->emitter->emit($event);
        } catch (\Exception $e) {
            throw new AppException('Error while creating an raw');
        }

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
        $this->validator->assertName($command->collection);

        try {
            $raw   = $this->repository->updateOneBySourceAndCollection($command->source, $command->collection, $command->data);
            $event = new Updated($raw);
            $this->emitter->emit($event);
        } catch (\Exception $e) {
            throw new AppException('Error while creating an raw');
        }

        return $raw;
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
        $this->validator->assertName($command->collection);

        $raw = $this->repository->findOneBySourceAndCollection($command->source, $command->collection);

        try {
            $affectedRows = $this->repository->deleteOneBySourceAndCollection($command->source, $command->collection);
            $event        = new Deleted($raw);
            $this->emitter->emit($event);
        } catch (\Exception $e) {
            throw new AppException('Error while creating an raw');
        }

        return $affectedRows;
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

        $raw = $this->repository->getAllBySourceAndCollections($command->source);

        try {
            $affectedRows = $this->repository->deleteBySource($command->source);
            $event        = new DeletedMulti($raw);
            $this->emitter->emit($event);
        } catch (\Exception $e) {
            throw new AppException('Error while creating an raw');
        }

        return $affectedRows;
    }
}
