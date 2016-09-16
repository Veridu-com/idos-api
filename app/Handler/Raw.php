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
use App\Exception\Create;
use App\Exception\Update;
use App\Exception\Validate;
use App\Exception\NotFound;
use App\Repository\RawInterface;
use App\Validator\Raw as RawValidator;
use Interop\Container\ContainerInterface;
use League\Event\Emitter;
use Respect\Validation\Exceptions\ValidationException;

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
        try {
            $this->validator->assertSource($command->source);
            $this->validator->assertName($command->collection);
        } catch (ValidationException $e) {
            throw new Validate\RawException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        try {
            $entity = $this->repository->findOne($command->source, $command->collection);

            throw new Create\RawException('Error while trying to create raw', 500, $e);
        } catch (NotFound $e) {

        }

        $raw = $this->repository->create(
            [
                'source'     => $command->source,
                'collection' => $command->collection,
                'data'       => $command->data,
                'created_at' => time()
            ]
        );

        try {
            $raw   = $this->repository->save($raw);
            $event = new Created($raw);

            $this->emitter->emit($event);
        } catch (\Exception $e) {
            throw new Create\RawException('Error while trying to create raw', 500, $e);
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
        try {
            $this->validator->assertSource($command->source);
            $this->validator->assertName($command->collection);
        } catch (ValidationException $e) {
            throw new Validate\RawException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        $entity = $this->repository->findOne($command->source, $command->collection);
        $entity->source = $command->source;
        $entity->data = $command->data;

        try {
            $entity   = $this->repository->save($entity);

            $event = new Updated($entity);
            $this->emitter->emit($event);
        } catch (\Exception $e) {
            throw new Update\RawException('Error while trying to update raw', 500, $e);
        }

        return $entity;
    }
}
