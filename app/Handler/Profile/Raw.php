<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Handler\Profile;

use App\Command\Profile\Raw\CreateNew;
use App\Command\Profile\Raw\DeleteAll;
use App\Command\Profile\Raw\Upsert;
use App\Entity\Profile\Raw as RawEntity;
use App\Exception\Create;
use App\Exception\NotFound;
use App\Exception\Update;
use App\Exception\Validate;
use App\Factory\Event;
use App\Handler\HandlerInterface;
use App\Repository\Profile\ProcessInterface;
use App\Repository\Profile\RawInterface;
use App\Repository\Profile\SourceInterface;
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
     * @var \App\Repository\Profile\RawInterface
     */
    private $repository;
    /**
     * Source Repository instance.
     *
     * @var \App\Repository\Profile\SourceInterface
     */
    private $sourceRepository;
    /**
     * Process Repository instance.
     *
     * @var \App\Repository\Profile\ProcessInterface
     */
    private $processRepository;
    /**
     * Raw Validator instance.
     *
     * @var \App\Validator\Profile\Raw
     */
    private $validator;
    /**
     * Event factory instance.
     *
     * @var \App\Factory\Event
     */
    private $eventFactory;
    /**
     * Event emitter instance.
     *
     * @var \League\Event\Emitter
     */
    private $emitter;

    /**
     * {@inheritdoc}
     */
    public static function register(ContainerInterface $container) : void {
        $container[self::class] = function (ContainerInterface $container) : HandlerInterface {
            $repositoryFactory = $container->get('repositoryFactory');

            return new \App\Handler\Profile\Raw(
                $repositoryFactory
                    ->create('Profile\Raw'),
                $repositoryFactory
                    ->create('Profile\Source'),
                $repositoryFactory
                    ->create('Profile\Process'),
                $container
                    ->get('validatorFactory')
                    ->create('Profile\Raw'),
                $container
                    ->get('eventFactory'),
                $container
                    ->get('eventEmitter')
            );
        };
    }

    /**
     * Class constructor.
     *
     * @param \App\Repository\Profile\RawInterface     $repository
     * @param \App\Repository\Profile\SourceInterface  $sourceRepository
     * @param \App\Repository\Profile\ProcessInterface $processRepository
     * @param \App\Validator\Profile\Raw               $validator
     * @param \App\Factory\Event                       $eventFactory
     * @param \League\Event\Emitter                    $emitter
     *
     * @return void
     */
    public function __construct(
        RawInterface $repository,
        SourceInterface $sourceRepository,
        ProcessInterface $processRepository,
        RawValidator $validator,
        Event $eventFactory,
        Emitter $emitter
    ) {
        $this->repository          = $repository;
        $this->sourceRepository    = $sourceRepository;
        $this->processRepository   = $processRepository;
        $this->validator           = $validator;
        $this->eventFactory        = $eventFactory;
        $this->emitter             = $emitter;
    }

    /**
     * Creates a new raw data in the given source.
     *
     * @param \App\Command\Profile\Raw\CreateNew $command
     *
     * @see \App\Repository\DBRaw::findOne
     * @see \App\Repository\DBRaw::create
     * @see \App\Repository\DBRaw::save
     *
     * @throws \App\Exception\Validate\Profile\RawException
     * @throws \App\Exception\Create\Profile\RawException
     *
     * @return \App\Entity\Profile\Raw
     */
    public function handleCreateNew(CreateNew $command) : RawEntity {
        try {
            $this->validator->assertSource($command->source, 'source');
            $this->validator->assertUser($command->user, 'user');
            $this->validator->assertName($command->collection, 'collection');
            $this->validator->assertCredential($command->credential, 'credential');
        } catch (ValidationException $e) {
            throw new Validate\Profile\RawException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        // We must assert thet there is no raw data with the given source and collection
        try {
            $entity = $this->repository->findOne($command->collection, $command->source);
            throw new Create\Profile\RawException('Error while trying to create raw', 500);
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
            $raw = $this->repository->save($raw);

            $process = $this->processRepository->findOneBySourceId($command->source->id);

            $event = $this->eventFactory->create(
                'Profile\\Raw\\Created',
                $raw,
                $command->user,
                $command->source,
                $process,
                $command->credential
            );

            $this->emitter->emit($event);
        } catch (\Exception $e) {
            throw new Create\Profile\RawException('Error while trying to create a raw', 500, $e);
        }

        return $raw;
    }

    /**
     * Deletes the raw data of a user.
     *
     * @param \App\Command\Profile\Raw\DeleteAll $command
     *
     * @see \App\Repository\DBRaw::findOne
     * @see \App\Repository\DBRaw::create
     * @see \App\Repository\DBRaw::save
     *
     * @throws \App\Exception\Validate\Profile\RawException
     * @throws \App\Exception\Create\Profile\RawException
     *
     * @return int
     */
    public function handleDeleteAll(DeleteAll $command) : int {
        try {
            $this->validator->assertUser($command->user, 'user');
        } catch (ValidationException $e) {
            throw new Validate\Profile\RawException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        try {
            $affectedRows = 0;

            $sourceNameInput = $command->queryParams['source'] ?? null;

            if ($sourceNameInput) {
                $userSources = $this->sourceRepository->getByUserIdAndName($command->user->id, $sourceNameInput);
            } else {
                $userSources = $this->sourceRepository->getByUserId($command->user->id);
            }

            foreach ($userSources as $source) {
                $affectedRows += $this->repository->deleteBySource($source);
            }

            return $affectedRows;
        } catch (NotFound $e) {
            throw new Create\Profile\RawException('Error while trying to delete raw data', 500, $e);
        }
    }

    /**
     * Creates or updates a raw data in the given source.
     *
     * @param \App\Command\Profile\Raw\Upsert $command
     *
     * @see \App\Repository\DBRaw::findOne
     * @see \App\Repository\DBRaw::create
     * @see \App\Repository\DBRaw::save
     *
     * @throws \App\Exception\Validate\Profile\RawException
     * @throws \App\Exception\Create\Profile\RawException
     *
     * @return \App\Entity\Profile\Raw
     */
    public function handleUpsert(Upsert $command) : RawEntity {
        try {
            $this->validator->assertSource($command->source, 'source');
            $this->validator->assertName($command->collection, 'collection');
            $this->validator->assertCredential($command->credential, 'credential');
        } catch (ValidationException $e) {
            throw new Validate\Profile\RawException(
                $e->getMessage(),
                400,
                $e
            );
        }

        $entity    = null;
        $inserting = false;

        try {
            $entity = $this->repository->findOne($command->collection, $command->source);

            $entity->source     = $command->source;
            $entity->data       = $command->data;
            $entity->updated_at = time();
        } catch (NotFound $e) {
        }

        $process = $this->processRepository->findOneBySourceId($command->source->id);

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

            if ($inserting) {
                $event = $this->eventFactory->create(
                    'Profile\\Raw\\Created',
                    $entity,
                    $command->user,
                    $command->source,
                    $process,
                    $command->credential
                );
            } else {
                $event = $this->eventFactory->create(
                    'Profile\\Raw\\Updated',
                    $entity,
                    $command->user,
                    $command->source,
                    $process,
                    $command->credential
                );
            }

            $this->emitter->emit($event);
        } catch (\Exception $e) {
            if ($inserting) {
                throw new Create\Profile\RawException('Error while trying to create raw', 500, $e);
            }

            throw new Update\Profile\RawException('Error while trying to update raw', 500, $e);
        }

        return $entity;
    }
}
