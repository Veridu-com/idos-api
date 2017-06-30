<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Handler\Profile;

use App\Command\CommandInterface;
use App\Command\Profile\Raw\CreateNew;
use App\Command\Profile\Raw\DeleteAll;
use App\Command\Profile\Raw\ListAll;
use App\Command\Profile\Raw\UpsertOne;
use App\Entity\Profile\Raw as RawEntity;
use App\Entity\Profile\Source;
use App\Exception\Create;
use App\Exception\NotFound;
use App\Exception\Update;
use App\Exception\Validate;
use App\Factory\Entity;
use App\Factory\Event;
use App\Handler\HandlerInterface;
use App\Repository\RepositoryInterface;
use App\Validator\Profile\Raw as RawValidator;
use Illuminate\Support\Collection;
use Interop\Container\ContainerInterface;
use League\Event\Emitter;
use League\Flysystem\Filesystem;
use Respect\Validation\Exceptions\ValidationException;

/**
 * Handles Raw commands.
 */
class Raw implements HandlerInterface {
    /**
     * File System instance.
     *
     * @var \League\Flysystem\Filesystem
     */
    private $fileSystem;
    /**
     * Entity Factory instance.
     *
     * @var \App\Factory\Entity
     */
    private $entityFactory;
    /**
     * Source Repository instance.
     *
     * @var \App\Repository\RepositoryInterface
     */
    private $sourceRepository;
    /**
     * Process Repository instance.
     *
     * @var \App\Repository\RepositoryInterface
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
     * Generates a base path based on source values.
     *
     * @param \App\Entity\Source $source
     *
     * @throws \RuntimeException
     *
     * @return string
     */
    private function getBasePath(Source $source) : string {
        if (! isset($source->id)) {
            throw new \RuntimeException('Invalid source id');
        }

        if (! isset($source->name)) {
            throw new \RuntimeException('Invalid source name');
        }

        return sprintf(
            '%s/%s',
            $source->name,
            md5((string) $source->id)
        );
    }

    /**
     * Generates a file name based on command parameters.
     *
     * @param \App\Command\CommandInterface $command
     *
     * @throws \RuntimeException
     *
     * @return string
     */
    private function getFileName(CommandInterface $command) : string {
        if (! property_exists($command, 'source')) {
            throw new \RuntimeException('Invalid source');
        }

        if (! property_exists($command, 'collection')) {
            throw new \RuntimeException('Invalid collection name');
        }

        return sprintf(
            '%s/%s.data',
            $this->getBasePath($command->source),
            $command->collection
        );
    }

    /**
     * {@inheritdoc}
     */
    public static function register(ContainerInterface $container) : void {
        $container[self::class] = function (ContainerInterface $container) : HandlerInterface {
            $fileSystem        = $container->get('fileSystem');
            $repositoryFactory = $container->get('repositoryFactory');

            return new \App\Handler\Profile\Raw(
                $fileSystem('raw'),
                $container
                    ->get('entityFactory'),
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
     * @param \League\Flysystem\Filesystem        $fileSystem
     * @param \App\Factory\Entity                 $entityFactory
     * @param \App\Repository\RepositoryInterface $sourceRepository
     * @param \App\Repository\RepositoryInterface $processRepository
     * @param \App\Validator\Profile\Raw          $validator
     * @param \App\Factory\Event                  $eventFactory
     * @param \League\Event\Emitter               $emitter
     *
     * @return void
     */
    public function __construct(
        Filesystem $fileSystem,
        Entity $entityFactory,
        RepositoryInterface $sourceRepository,
        RepositoryInterface $processRepository,
        RawValidator $validator,
        Event $eventFactory,
        Emitter $emitter
    ) {
        $this->fileSystem        = $fileSystem;
        $this->entityFactory     = $entityFactory;
        $this->sourceRepository  = $sourceRepository;
        $this->processRepository = $processRepository;
        $this->validator         = $validator;
        $this->eventFactory      = $eventFactory;
        $this->emitter           = $emitter;
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
        } catch (ValidationException $exception) {
            throw new Validate\Profile\RawException(
                $exception->getFullMessage(),
                400,
                $exception
            );
        }

        $fileName = $this->getFileName($command);

        try {
            if ($this->fileSystem->has($fileName)) {
                throw new Create\Profile\RawException('Error while trying to create raw, collection already exists.', 500);
            }

            $raw = $this->entityFactory->create('Profile\Raw');
            $raw->hydrate(
                [
                    'source_id'  => $command->source->getEncodedId(),
                    'collection' => $command->collection,
                    'data'       => $command->data,
                    'created_at' => time()
                ]
            );

            $serialized = $raw->serialize();

            $this->fileSystem->write($fileName, $serialized['data']);

            $process = $this->processRepository->findOneBySourceId($command->source->id);

            $event = $this->eventFactory->create(
                'Profile\Raw\Created',
                $raw,
                $command->user,
                $command->source,
                $process,
                $command->credential
            );

            $this->emitter->emit($event);

            return $raw;
        } catch (\Exception $exception) {
            throw new Create\Profile\RawException('Error while trying to create raw', 500, $exception);
        }
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
        } catch (ValidationException $exception) {
            throw new Validate\Profile\RawException(
                $exception->getFullMessage(),
                400,
                $exception
            );
        }

        try {
            $affectedRows = 0;

            $sourceNameInput = $command->queryParams['source'] ?? null;

            if ($sourceNameInput) {
                $sources = $this->sourceRepository->getByUserIdAndName($command->user->id, $sourceNameInput);
            } else {
                $sources = $this->sourceRepository->getByUserId($command->user->id);
            }

            foreach ($sources as $source) {
                $basePath = $this->getBasePath($source);
                $affectedRows += count($this->fileSystem->listFiles($basePath));
                $this->fileSystem->deleteDir($basePath);
            }

            return $affectedRows;
        } catch (NotFound $exception) {
            throw new Create\Profile\RawException('Error while trying to delete raw data', 500, $exception);
        }
    }

    /**
     * List all raw data in the given source.
     *
     * @param \App\Command\Profile\Raw\ListAll $command
     *
     * @throws \App\Exception\Validate\Profile\RawException
     *
     * @return \Illuminate\Support\Collection
     */
    public function handleListAll(ListAll $command) : Collection {
        try {
            $this->validator->assertUser($command->user, 'user');
            $this->validator->assertNullableArray($command->queryParams, 'queryParams');
        } catch (ValidationException $exception) {
            throw new Validate\Profile\RawException(
                $exception->getFullMessage(),
                400,
                $exception
            );
        }

        foreach ($command->queryParams as $key => $value) {
            if (substr_compare($key, 'source:', 0, 7) === 0) {
                $command->queryParams[substr($key, 7)] = $value;
                unset($command->queryParams[$key]);
            }
        }

        $sources = $this->sourceRepository->getByUserIdFiltered($command->user->id, $command->queryParams);

        $filter = [];
        if (isset($command->queryParams['collection'])) {
            if (! preg_match('/^[a-zA-Z0-9]+(,[a-zA-Z0-9]+)*$/', $command->queryParams['collection'])) {
                throw new Validate\Profile\RawException('Invalid collection filter');
            }

            $filter = explode(',', $command->queryParams['collection']);
        }

        $filtered = count($filter) > 0;

        $entities = new Collection();
        foreach ($sources as $source) {
            $basePath = $this->getBasePath($source);
            foreach ($this->fileSystem->listFiles($basePath) as $file) {
                if (! preg_match('/^[a-zA-Z0-9]+$/', $file['filename'])) {
                    continue;
                }

                if ($file['size'] == 0) {
                    continue;
                }

                if (($filtered) && (! in_array($file['filename'], $filter))) {
                    continue;
                }

                $raw = $this->entityFactory->create(
                    'Profile\Raw',
                    [
                        'source_id'  => $source->getEncodedId(),
                        'collection' => $file['filename'],
                        'data'       => $this->fileSystem->read($file['path']),
                        'created_at' => $this->fileSystem->getTimestamp($file['path']),
                        'updated_at' => null
                    ]
                );
                $entities->push($raw);
            }
        }

        return $entities;
    }

    /**
     * Creates or updates a raw data in the given source.
     *
     * @param \App\Command\Profile\Raw\UpsertOne $command
     *
     * @throws \App\Exception\Validate\Profile\RawException
     * @throws \App\Exception\Create\Profile\RawException
     * @throws \App\Exception\Update\Profile\RawException
     *
     * @return \App\Entity\Profile\Raw
     */
    public function handleUpsertOne(UpsertOne $command) : RawEntity {
        try {
            $this->validator->assertSource($command->source, 'source');
            $this->validator->assertName($command->collection, 'collection');
            $this->validator->assertCredential($command->credential, 'credential');
        } catch (ValidationException $exception) {
            throw new Validate\Profile\RawException(
                $exception->getFullMessage(),
                400,
                $exception
            );
        }

        $fileName = $this->getFileName($command);

        try {
            $inserting = false;
            $createdAt = time();
            $updatedAt = null;
            if ($this->fileSystem->has($fileName)) {
                $inserting = true;
                $createdAt = $this->fileSystem->getTimestamp($fileName);
                $updatedAt = time();
            }

            $raw = $this->entityFactory->create('Profile\Raw');
            $raw->hydrate(
                [
                    'source_id'  => $command->source->getEncodedId(),
                    'collection' => $command->collection,
                    'data'       => $command->data,
                    'created_at' => $createdAt,
                    'updated_at' => $updatedAt
                ]
            );

            $serialized = $raw->serialize();

            $this->fileSystem->put($fileName, $serialized['data']);

            $process = $this->processRepository->findOneBySourceId($command->source->id);

            $eventName = 'Profile\Raw\Updated';
            if ($inserting) {
                $eventName = 'Profile\Raw\Created';
            }

            $event = $this->eventFactory->create(
                $eventName,
                $raw,
                $command->user,
                $command->source,
                $process,
                $command->credential
            );

            $this->emitter->emit($event);

            return $raw;
        } catch (\Exception $exception) {
            if ($inserting) {
                throw new Create\Profile\RawException('Error while trying to create raw', 500, $exception);
            }

            throw new Update\Profile\RawException('Error while trying to update raw', 500, $exception);
        }
    }
}
