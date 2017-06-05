<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Handler\Profile;

use App\Command\Profile\Feature\CreateNew;
use App\Command\Profile\Feature\DeleteAll;
use App\Command\Profile\Feature\DeleteOne;
use App\Command\Profile\Feature\UpdateOne;
use App\Command\Profile\Feature\Upsert;
use App\Command\Profile\Feature\UpsertBulk;
use App\Entity\Profile\Feature as FeatureEntity;
use App\Exception\Create;
use App\Exception\NotFound;
use App\Exception\Update;
use App\Exception\Upsert\Profile\FeatureException as UpsertException;
use App\Exception\Validate;
use App\Extension\RetrieveProcess;
use App\Factory\Event;
use App\Handler\HandlerInterface;
use App\Repository\Profile\FeatureInterface;
use App\Repository\Profile\ProcessInterface;
use App\Repository\Profile\SourceInterface;
use App\Validator\Profile\Feature as FeatureValidator;
use Illuminate\Support\Collection;
use Interop\Container\ContainerInterface;
use League\Event\Emitter;
use Respect\Validation\Exceptions\ValidationException;

/**
 * Handles Feature commands.
 */
class Feature implements HandlerInterface {
    use RetrieveProcess;

    /**
     * Feature Repository instance.
     *
     * @var \App\Repository\Profile\FeatureInterface
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
     * Feature Validator instance.
     *
     * @var \App\Validator\Profile\Feature
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

            return new \App\Handler\Profile\Feature(
                $repositoryFactory
                    ->create('Profile\Feature'),
                $repositoryFactory
                    ->create('Profile\Source'),
                $repositoryFactory
                    ->create('Profile\Process'),
                $container
                    ->get('validatorFactory')
                    ->create('Profile\Feature'),
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
     * @param \App\Repository\Profile\FeatureInterface $repository
     * @param \App\Repository\Profile\SourceInterface  $sourceRepository
     * @param \App\Repository\Profile\ProcessInterface $processRepository
     * @param \App\Validator\Profile\Feature           $validator
     * @param \App\Factory\Event                       $eventFactory
     * @param \League\Event\Emitter                    $emitter
     *
     * @return void
     */
    public function __construct(
        FeatureInterface $repository,
        SourceInterface $sourceRepository,
        ProcessInterface $processRepository,
        FeatureValidator $validator,
        Event $eventFactory,
        Emitter $emitter
    ) {
        $this->repository        = $repository;
        $this->sourceRepository  = $sourceRepository;
        $this->processRepository = $processRepository;
        $this->validator         = $validator;
        $this->eventFactory      = $eventFactory;
        $this->emitter           = $emitter;
    }

    /**
     * Creates a feature.
     *
     * @param \App\Command\Profile\Feature\CreateNew $command
     *
     * @see \App\Repository\DBFeature::save
     *
     * @throws \App\Exception\Validate\Profile\FeatureException
     * @throws \App\Exception\Create\Profile\FeatureException
     *
     * @return \App\Entity\Profile\Feature
     */
    public function handleCreateNew(CreateNew $command) : FeatureEntity {
        try {
            $this->validator->assertUser($command->user);
            $this->validator->assertCredential($command->credential);

            $this->validator->assertHandler($command->handler);
            $this->validator->assertLongName($command->name);

            $this->validator->assertName($command->type);
            $this->validator->assertNullableValue($command->value);

            $sourceName = 'profile';
            if ($command->source !== null) {
                $this->validator->assertSource($command->source);
                $sourceName = $command->source->name;
            }

            $this->validator->assertCredential($command->credential);
        } catch (ValidationException $exception) {
            throw new Validate\Profile\FeatureException(
                $exception->getFullMessage(),
                400,
                $exception
            );
        }

        $feature = $this->repository->create(
            [
                'user_id'    => $command->user->id,
                'source'     => $sourceName,
                'name'       => $command->name,
                'creator'    => $command->handler->id,
                'type'       => $command->type,
                'value'      => $command->value,
                'created_at' => time()
            ]
        );

        try {
            $feature = $this->repository->save($feature);
            $feature = $this->repository->hydrateRelations($feature);
            $process = $this->getRelatedProcess(
                $this->processRepository,
                $command->user->id,
                $this->getProcessEventName($command->source),
                $command->source ? $command->source : null
            );

            $event = $this->eventFactory->create(
                'Profile\\Feature\\Created',
                $feature,
                $command->user,
                $process,
                $command->credential,
                $command->source
            );
            $this->emitter->emit($event);
        } catch (\Exception $exception) {
            throw new Create\Profile\FeatureException('Error while trying to create a feature', 500, $exception);
        }

        return $feature;
    }

    /**
     * Updates a Feature.
     *
     * @param \App\Command\Profile\Feature\UpdateOne $command
     *
     * @see \App\Repository\DBFeature::findByUserIdAndSlug
     * @see \App\Repository\DBFeature::save
     *
     * @throws \App\Exception\Validate\Profile\FeatureException
     * @throws \App\Exception\Update\Profile\FeatureException
     *
     * @return \App\Entity\Profile\Feature
     */
    public function handleUpdateOne(UpdateOne $command) : FeatureEntity {
        try {
            $this->validator->assertUser($command->user);
            $this->validator->assertHandler($command->handler);
            $this->validator->assertId($command->featureId);
            $this->validator->assertName($command->type);
            $this->validator->assertNullableValue($command->value);
            $this->validator->assertCredential($command->credential);
        } catch (ValidationException $e) {
            throw new Validate\Profile\FeatureException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        $feature = $this->repository->findOne(
            $command->featureId,
            $command->handler->id,
            $command->user->id
        );

        $feature->type      = $command->type;
        $feature->value     = $command->value;
        $feature->updatedAt = time();

        try {
            $feature = $this->repository->save($feature);
            $feature = $this->repository->hydrateRelations($feature);

            $process = $this->getRelatedProcess(
                $this->processRepository,
                $command->user->id,
                $this->getProcessEventName($command->source),
                $command->source ? $command->source : null
            );

            $event = $this->eventFactory->create(
                'Profile\\Feature\\Updated',
                $feature,
                $command->user,
                $process,
                $command->credential,
                $command->source
            );
            $this->emitter->emit($event);
        } catch (\Exception $exception) {
            throw new Update\Profile\FeatureException('Error while trying to update a feature', 500, $exception);
        }

        return $feature;
    }

    /**
     * Creates or update a feature.
     *
     * @param \App\Command\Profile\Feature\Upsert $command
     *
     * @return \App\Entity\Profile\Feature
     */
    public function handleUpsert(Upsert $command) : FeatureEntity {
        try {
            $this->validator->assertUser($command->user);
            $this->validator->assertHandler($command->handler);
            $this->validator->assertLongName($command->name);
            $this->validator->assertName($command->type);
            $this->validator->assertNullableValue($command->value);
            $this->validator->assertCredential($command->credential);
        } catch (ValidationException $exception) {
            throw new Validate\Profile\FeatureException(
                $exception->getFullMessage(),
                400,
                $exception
            );
        }

        $sourceName = 'profile';
        if ($command->source !== null) {
            $this->validator->assertSource($command->source);
            $sourceName = $command->source->name;
        }

        try {
            $feature = $this->repository->create(
                [
                    'user_id'    => $command->user->id,
                    'source'     => $sourceName,
                    'name'       => $command->name,
                    'creator'    => $command->handler->id,
                    'type'       => $command->type,
                    'value'      => $command->value,
                    'created_at' => date('Y-m-d H:i:s')
                ]
            );

            $serialized = $feature->serialize();

            $this->repository->upsert(
                $feature,
                [
                    'user_id',
                    'source',
                    'creator',
                    'name'
                ],
                [
                    'type'       => $serialized['type'],
                    'value'      => $serialized['value'],
                    'updated_at' => date('Y-m-d H:i:s')
                ]
            );

            $feature = $this->repository->findOneByName(
                $command->name,
                $command->handler->id,
                $sourceName,
                $command->user->id
            );

            $process = $this->getRelatedProcess(
                $this->processRepository,
                $command->user->id,
                $this->getProcessEventName($command->source),
                $command->source ? $command->source : null
            );

            if ($feature->updatedAt) {
                $event = $this->eventFactory->create(
                    'Profile\\Feature\\Updated',
                    $feature,
                    $command->user,
                    $process,
                    $command->credential,
                    $command->source
                );
            } else {
                $event = $this->eventFactory->create(
                    'Profile\\Feature\\Created',
                    $feature,
                    $command->user,
                    $process,
                    $command->credential,
                    $command->source
                );
            }

            $this->emitter->emit($event);
        } catch (\Exception $exception) {
            throw new Create\Profile\FeatureException(
                'Error while trying to upsert a feature',
                404,
                $exception
            );
        }

        return $feature;
    }

    /**
     * Creates or update a feature.
     *
     * @param \App\Command\Profile\Feature\UpsertBulk $command
     *
     * @return \Illuminate\Support\Collection
     */
    public function handleUpsertBulk(UpsertBulk $command) : Collection {
        try {
            $this->validator->assertUser($command->user);
            $this->validator->assertCredential($command->credential);
            $this->validator->assertHandler($command->handler);
            $this->validator->assertFeatures($command->features);
        } catch (ValidationException $exception) {
            throw new Validate\Profile\FeatureException(
                $exception->getFullMessage(),
                400,
                $exception
            );
        }

        if (count($command->features) > 500) {
            throw new ValidationException('Your bulk upsert cannot exceed 500 items.');
        }

        $sources   = [];
        $perSource = [];

        // next loop will:
        // put "source->name" on the feature register retrieved from a sourceRepository->find
        // add to $perSource so we can send events by source
        try {
            $features = [];
            $this->repository->beginTransaction();
            foreach ($command->features as $feature) {
                $sourceName = 'profile';
                if (isset($feature['source_id'])) {
                    // gets the source name for every feature that has a source
                    if (isset($sources[$feature['source_id']])) {
                        $source = $sources[$feature['source_id']];
                    } else {
                        $source = $this->sourceRepository->find(
                            $feature['decoded_source_id']
                        );
                        $sources[$feature['source_id']] = $source;
                    }

                    $sourceName = $source->name;
                }


                $entity = $this->repository->create(
                    [
                        'user_id'    => $command->user->id,
                        'source'     => $sourceName,
                        'name'       => $feature['name'],
                        'creator'    => $command->handler->id,
                        'type'       => $feature['type'],
                        'value'      => $feature['value'],
                        'created_at' => date('Y-m-d H:i:s')
                    ]
                );

                $serialized                         = $entity->serialize();
                $perSource[$feature['source_id'] ?? 'profile'][] = $entity;

                $this->repository->upsert(
                    $entity,
                    [
                        'user_id',
                        'source',
                        'creator',
                        'name'
                    ],
                    [
                        'type'       => $serialized['type'],
                        'value'      => $serialized['value'],
                        'updated_at' => date('Y-m-d H:i:s')
                    ]
                );

                $features[] = $entity;
            }

            // creates 1 event per source
            // sourceId will be 0 to null sources
            foreach ($perSource as $sourceId => $sourceFeatures) {
                $source  = $sources[$sourceId] ?? null;
                $process = $this->getRelatedProcess(
                    $this->processRepository,
                    $command->user->id,
                    $this->getProcessEventName($source),
                    $source
                );

                $event = $this->eventFactory->create(
                    'Profile\\Feature\\CreatedBulk',
                    $sourceFeatures,
                    $command->user,
                    $process,
                    $command->credential,
                    $source
                );
                $this->emitter->emit($event);
            }

            $this->repository->commit();

            return new Collection($features);
        } catch (\Exception $exception) {
            $this->repository->rollBack();
            throw new UpsertException('Failed to execute upsert bulk', 500, $exception);
        }
    }
    /**
     * Deletes a Feature.
     *
     * @param \App\Command\Profile\Feature\DeleteOne $command
     *
     * @see \App\Repository\DBFeature::findByUserIdAndSlug
     * @see \App\Repository\DBFeature::delete
     *
     * @throws \App\Exception\Validate\Profile\FeatureException
     * @throws \App\Exception\NotFound\Profile\FeatureException
     *
     * @return int
     */
    public function handleDeleteOne(DeleteOne $command) : int {
        try {
            $this->validator->assertUser($command->user);
            $this->validator->assertHandler($command->handler);
            $this->validator->assertId($command->featureId);
            $this->validator->assertCredential($command->credential);
        } catch (ValidationException $exception) {
            throw new Validate\Profile\FeatureException(
                $exception->getFullMessage(),
                400,
                $exception
            );
        }

        $feature = $this->repository->findOne($command->featureId, $command->handler->id, $command->user->id);

        $affectedRows = $this->repository->delete($feature->id);
        if (! $affectedRows) {
            throw new NotFound\Profile\FeatureException('No features found for deletion', 404);
        }

        $event = $this->eventFactory->create('Profile\\Feature\\Deleted', $feature, $command->credential);
        $this->emitter->emit($event);

        return $affectedRows;
    }

    /**
     * Deletes all settings ($command->userId).
     *
     * @param \App\Command\Profile\Feature\DeleteAll $command
     *
     * @see \App\Repository\DBFeature::findByUserId
     * @see \App\Repository\DBFeature::deleteByUserId
     *
     * @throws \App\Exception\Validate\Profile\FeatureException
     *
     * @return int
     */
    public function handleDeleteAll(DeleteAll $command) : int {
        try {
            $this->validator->assertUser($command->user);
            $this->validator->assertHandler($command->handler);
            $this->validator->assertArray($command->queryParams);
            $this->validator->assertCredential($command->credential);
        } catch (ValidationException $exception) {
            throw new Validate\Profile\FeatureException(
                $exception->getFullMessage(),
                400,
                $exception
            );
        }

        $deletedFeatures = $this->repository->getByHandlerIdAndUserId(
            $command->handler->id,
            $command->user->id,
            $command->queryParams
        );

        $affectedRows = 0;
        $this->repository->beginTransaction();
        foreach ($deletedFeatures as $deletedFeature) {
            $affectedRows += $this->repository->delete($deletedFeature->id);
        }
        $this->repository->commit();

        $event = $this->eventFactory->create('Profile\\Feature\\DeletedMulti', $deletedFeatures, $command->credential);
        $this->emitter->emit($event);

        return $affectedRows;
    }

    /**
     * Gets the process event name.
     *
     * @param mixed $source The source
     *
     * @return string The process event name.
     */
    private function getProcessEventName($source = null) : string {
        return sprintf('idos:feature.%s.created', $source ? $source->name : 'profile');
    }
}
