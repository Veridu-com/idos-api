<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Handler\Profile;

use App\Command\Profile\Raw\CreateNew;
use App\Command\Profile\Raw\UpdateOne;
use App\Command\Profile\Raw\Upsert;
use App\Entity\Profile\Raw as RawEntity;
use App\Event\Profile\Raw\Created;
use App\Event\Profile\Raw\Updated;
use App\Exception\Create;
use App\Exception\NotFound;
use App\Exception\Update;
use App\Exception\Validate;
use App\Handler\HandlerInterface;
use App\Repository\Profile\RawInterface;
use App\Validator\Profile\Raw as RawValidator;
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
     * @var App\Repository\Profile\RawInterface
     */
    protected $repository;
    /**
     * Raw Validator instance.
     *
     * @var App\Validator\Profile\Raw
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
            return new \App\Handler\Profile\Raw(
                $container
                    ->get('repositoryFactory')
                    ->create('Profile\Raw'),
                $container
                    ->get('validatorFactory')
                    ->create('Profile\Raw'),
                $container
                    ->get('eventEmitter')
            );
        };
    }

    /**
     * Class constructor.
     *
     * @param App\Repository\Profile\RawInterface $repository
     * @param App\Validator\Profile\Raw           $validator
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
     * @param App\Command\Profile\Raw\CreateNew $command
     *
     * @see App\Repository\DBRaw::findOne
     * @see App\Repository\DBRaw::create
     * @see App\Repository\DBRaw::save
     *
     * @throws App\Exception\Validate\RawException
     * @throws App\Exception\Create\RawException
     *
     * @return App\Entity\Raw
     */
    public function handleCreateNew(CreateNew $command) : RawEntity {
        try {
            $this->validator->assertSource($command->source);
            $this->validator->assertName($command->collection);
        } catch (ValidationException $e) {
            throw new Validate\Profile\RawException(
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
            throw new Create\Profile\RawException('Error while trying to create a raw', 500, $e);
        }

        return $raw;
    }

    /**
     * Updates a raw data from a given source.
     *
     * @param App\Command\Profile\Raw\UpdateOne $command
     *
     * @see App\Repository\DBRaw::findOne
     * @see App\Repository\DBRaw::save
     *
     * @throws App\Exception\Validate\RawException
     * @throws App\Exception\Update\RawException
     *
     * @return App\Entity\Raw
     */
    public function handleUpdateOne(UpdateOne $command) : RawEntity {
        try {
            $this->validator->assertSource($command->source);
            $this->validator->assertName($command->collection);
        } catch (ValidationException $e) {
            throw new Validate\Profile\RawException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        $entity         = $this->repository->findOne($command->source, $command->collection);
        $entity->source = $command->source;
        $entity->data   = $command->data;

        try {
            $entity = $this->repository->save($entity);

            $event = new Updated($entity);
            $this->emitter->emit($event);
        } catch (\Exception $e) {
            throw new Update\RawException('Error while trying to update raw', 500, $e);
        }

        return $entity;
    }

    /**
     * Creates or updates a raw data in the given source.
     *
     * @param App\Command\Raw\Upsert $command
     *
     * @see App\Repository\DBRaw::findOne
     * @see App\Repository\DBRaw::create
     * @see App\Repository\DBRaw::save
     *
     * @throws App\Exception\Validate\RawException
     * @throws App\Exception\Create\RawException
     *
     * @return App\Entity\Raw
     */
    public function handleUpsert(Upsert $command) : RawEntity {
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

        $entity    = null;
        $inserting = false;

        try {
            $entity = $this->repository->findOne($command->source, $command->collection);

            $entity->source     = $command->source;
            $entity->data       = $command->data;
            $entity->updated_at = time();
        } catch (NotFound $e) {
        }

        if ($entity === null) {
            $inserting = true;
            $entity    = $this->repository->create(
                [
                    'source'     => $command->source,
                    'collection' => $command->collection,
                    'data'       => $command->data,
                    'created_at' => time(),
                    'updated_at' => null
                ]
            );
        }

        try {
            $entity = $this->repository->save($entity);
            $event  = $inserting ? new Created($entity) : new Updated($entity);

            $this->emitter->emit($event);
        } catch (\Exception $e) {
            if ($inserting) {
                throw new Create\RawException('Error while trying to create raw', 500, $e);
            } else {
                throw new Update\RawException('Error while trying to update raw', 500, $e);
            }
        }

        return $entity;
    }
}
