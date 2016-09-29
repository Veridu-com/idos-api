<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Handler\Profile;

use App\Command\CommandInterface;
use App\Command\Profile\Feature\CreateNew;
use App\Command\Profile\Feature\DeleteAll;
use App\Command\Profile\Feature\DeleteOne;
use App\Command\Profile\Feature\UpdateOne;
use App\Command\Profile\Feature\Upsert;
use App\Command\Profile\Feature\UpsertBulk;
use App\Entity\Profile\Feature as FeatureEntity;
use App\Entity\Profile\Process;
use App\Exception\Create;
use App\Exception\NotFound;
use App\Exception\Update;
use App\Exception\Validate;
use App\Factory\Event;
use App\Handler\HandlerInterface;
use App\Repository\Profile\FeatureInterface;
use App\Repository\Profile\ProcessInterface;
use App\Repository\Profile\SourceInterface;
use App\Validator\Profile\Feature as FeatureValidator;
use Interop\Container\ContainerInterface;
use League\Event\Emitter;
use Respect\Validation\Exceptions\ValidationException;

/**
 * Handles Feature commands.
 */
class Feature implements HandlerInterface {
    /**
     * Feature Repository instance.
     *
     * @var App\Repository\Profile\FeatureInterface
     */
    private $repository;

    /**
     * Source Repository instance.
     *
     * @var App\Repository\SourceInterface
     */
    private $sourceRepository;

    /**
     * Feature Validator instance.
     *
     * @var App\Validator\Profile\Feature
     */
    private $validator;
    /**
     * Event factory instance.
     *
     * @var App\Factory\Event
     */
    private $eventFactory;

    /**
     * Event emitter instance.
     *
     * @var League\Event\Emitter
     */
    private $emitter;

    /**
     * {@inheritdoc}
     */
    public static function register(ContainerInterface $container) {
        $container[self::class] = function (ContainerInterface $container) {
            return new \App\Handler\Profile\Feature(
                $container
                    ->get('repositoryFactory')
                    ->create('Profile\Feature'),
                $container
                    ->get('repositoryFactory')
                    ->create('Profile\Source'),
                $container
                    ->get('repositoryFactory')
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
     * @param App\Repository\FeatureInterface $repository
     * @param App\Validator\Feature           $validator
     * @param App\Factory\Event               $eventFactory
     * @param \League\Event\Emitter           $emitter
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
     * @param App\Command\Profile\Feature\CreateNew $command
     *
     * @see App\Repository\DBFeature::save
     *
     * @throws App\Exception\Validate\Profile\FeatureException
     * @throws App\Exception\Create\Profile\FeatureException
     *
     * @return App\Entity\Feature
     */
    public function handleCreateNew(CreateNew $command) : FeatureEntity {
        try {
            $this->validator->assertUser($command->user);
            $this->validator->assertCredential($command->credential);

            $this->validator->assertService($command->service);
            $this->validator->assertLongName($command->name);

            $this->validator->assertName($command->type);
            $this->validator->assertNullableValue($command->value);
            $sourceName = null;
            if ($command->source !== null) {
                $this->validator->assertSource($command->source);
                $sourceName = $command->source->name;
            }
        } catch (ValidationException $e) {
            throw new Validate\Profile\FeatureException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        $feature = $this->repository->create(
            [
                'user_id'    => $command->user->id,
                'source'     => $sourceName,
                'name'       => $command->name,
                'creator'    => $command->service->id,
                'type'       => $command->type,
                'value'      => $command->value,
                'created_at' => time()
            ]
        );

        try {
            $feature = $this->repository->save($feature);
            $feature = $this->repository->hydrateRelations($feature);

            $process = $this->getRelatedProcess($command->user->id, $command->source ? $command->source  : null);
            $event   = $this->eventFactory->create('Profile\\Feature\\Created', $feature, $command->user, $command->credential, $process, $command->source);
            $this->emitter->emit($event);
        } catch (\Exception $e) {

            throw new Create\Profile\FeatureException('Error while trying to create a feature', 500, $e);
        }

        return $feature;
    }

    /**
     * Updates a Feature.
     *
     * @param App\Command\Profile\Feature\UpdateOne $command
     *
     * @see App\Repository\DBFeature::findByUserIdAndSlug
     * @see App\Repository\DBFeature::save
     *
     * @throws App\Exception\Validate\Profile\FeatureException
     * @throws App\Exception\Update\Profile\FeatureException
     *
     * @return App\Entity\Feature
     */
    public function handleUpdateOne(UpdateOne $command) : FeatureEntity {
        try {
            $this->validator->assertUser($command->user);
            $this->validator->assertService($command->service);
            $this->validator->assertId($command->featureId);
            $this->validator->assertName($command->type);
            $this->validator->assertNullableValue($command->value);
        } catch (ValidationException $e) {
            throw new Validate\Profile\FeatureException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        $feature = $this->repository->findOneBy(
            [
                'user_id' => $command->user->id,
                'creator' => $command->service->id,
                'id'      => $command->featureId
            ]
        );

        $feature->type      = $command->type;
        $feature->value     = $command->value;
        $feature->updatedAt = time();

        try {
            $feature = $this->repository->save($feature);
            $feature = $this->repository->hydrateRelations($feature);

            $process = $this->getRelatedProcess($command->user->id, $command->source ? $command->source : null);
            $event   = $this->eventFactory->create('Profile\\Feature\\Updated', $feature, $command->user, $command->credential, $process, $command->source);
            $this->emitter->emit($event);
        } catch (\Exception $e) {
            throw new Update\Profile\FeatureException('Error while trying to update a feature', 500, $e);
        }

        return $feature;
    }

    /**
     * Deletes all settings ($command->userId).
     *
     * @param App\Command\Profile\Feature\DeleteAll $command
     *
     * @see App\Repository\DBFeature::findByUserId
     * @see App\Repository\DBFeature::deleteByUserId
     *
     * @throws App\Exception\Validate\Profile\FeatureException
     *
     * @return int
     */
    public function handleDeleteAll(DeleteAll $command) : int {
        try {
            $this->validator->assertUser($command->user);
            $this->validator->assertService($command->service);
            $this->validator->assertArray($command->queryParams);
        } catch (ValidationException $e) {
            throw new Validate\Profile\FeatureException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        $deletedFeatures = $this->repository->findBy(
            [
                'user_id' => $command->user->id,
                'creator' => $command->service->id
            ],
            $command->queryParams
        );

        $affectedRows = 0;

        foreach ($deletedFeatures as $deletedFeature) {
            $affectedRows += $this->repository->delete($deletedFeature->id);
        }

        $event = $this->eventFactory->create('Profile\\Feature\\DeletedMulti', $deletedFeatures);
        $this->emitter->emit($event);

        return $affectedRows;
    }

    /**
     * Deletes a Feature.
     *
     * @param App\Command\Profile\Feature\DeleteOne $command
     *
     * @see App\Repository\DBFeature::findByUserIdAndSlug
     * @see App\Repository\DBFeature::delete
     *
     * @throws App\Exception\Validate\Profile\FeatureException
     * @throws App\Exception\NotFound\Profile\FeatureException
     *
     * @return void
     */
    public function handleDeleteOne(DeleteOne $command) : int {
        try {
            $this->validator->assertUser($command->user);
            $this->validator->assertService($command->service);
            $this->validator->assertId($command->featureId);
        } catch (ValidationException $e) {
            throw new Validate\Profile\FeatureException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        $feature = $this->repository->findOneBy(
            [
            'user_id' => $command->user->id,
            'creator' => $command->service->id,
            'id'      => $command->featureId
            ]
        );

        $affectedRows = $this->repository->delete($feature->id);

        if (! $affectedRows) {
            throw new NotFound\Profile\FeatureException('No features found for deletion', 404);
        }

        $event = $this->eventFactory->create('Profile\\Feature\\Deleted', $feature);
        $this->emitter->emit($event);

        return $affectedRows;
    }

    /**
     * Creates or update a feature.
     *
     * @param App\Command\Profile\Feature\Upsert $command
     *
     * @return App\Entity\Profile\Feature
     */
    public function handleUpsert(Upsert $command) : FeatureEntity {
        try {
            $this->validator->assertUser($command->user);
            $this->validator->assertService($command->service);
            $this->validator->assertLongName($command->name);
            $this->validator->assertName($command->type);
            $this->validator->assertNullableValue($command->value);
        } catch (ValidationException $e) {
            throw new Validate\Profile\FeatureException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        $sourceName = null;
        if ($command->source !== null) {
            $this->validator->assertSource($command->source);
            $sourceName = $command->source->name;
        }

        $feature   = null;
        $inserting = false;
        try {
            $feature = $this->repository->findOneByName(
                $command->user->id,
                $sourceName,
                $command->service->id,
                $command->name
            );

            $feature->type      = $command->type;
            $feature->value     = $command->value;
            $feature->updatedAt = time();
        } catch (NotFound $e) {
            $inserting = true;

            $feature = $this->repository->create(
                [
                    'user_id'    => $command->user->id,
                    'source'     => $sourceName,
                    'name'       => $command->name,
                    'creator'    => $command->service->id,
                    'type'       => $command->type,
                    'value'      => $command->value,
                    'created_at' => time()
                ]
            );
        }

        try {
            $feature = $this->repository->save($feature);
            $feature = $this->repository->hydrateRelations($feature);
            $process = $this->getRelatedProcess($command->user->id, $command->source ? $command->source  : null);

            if ($inserting) {
                $event = $this->eventFactory->create('Profile\\Feature\\Created', $feature, $command->user, $command->credential, $process, $command->source);
            } else {
                $event = $this->eventFactory->create('Profile\\Feature\\Updated', $feature, $command->user, $command->credential, $process, $command->source);
            }
            $this->emitter->emit($event);
        } catch (\Exception $e) {
            throw new NotFound\Profile\FeatureException('Error while trying to upsert a feature', 404, $e);
        }

        return $feature;
    }
    /**
     * Creates or update a feature.
     *
     * @param App\Command\Profile\Feature\Upsert $command
     *
     * @return bool
     */
    public function handleUpsertBulk(UpsertBulk $command) : bool {
        try {
            $this->validator->assertUser($command->user);
            $this->validator->assertCredential($command->credential);
            $this->validator->assertService($command->service);
            $this->validator->assertFeatures($command->features);
        } catch (ValidationException $e) {
            throw new Validate\Profile\FeatureException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        $features          = $command->features;
        $sources           = [];
        $featuresPerSource = [];

        // next loop will:
        // put "source->name" on the feature register retrieved from a sourceRepository->find
        // add to featuresPerSource so we can send events by source
        foreach ($features as $key => $feature) {
            // sourceId => sourceEntity - if source is null add to the index 0
            $sourceId                       = isset($feature['source_id']) ? $feature['source_id'] : 0;
            $featuresPerSource[$sourceId][] = $feature;

            // gets the source name for every feature that has a source
            if (isset($feature['source_id'])) {
                if (! isset($sources[$feature['source_id']])) {
                    $source = $this->sourceRepository->find($feature['decoded_source_id']);
                } else {
                    $source = $sources[$feature['source_id']];
                }

                $features[$key]['source']       = $source->name;
                $sources[$feature['source_id']] = $source;
            }

        }

        $success = $this->repository->upsertBulk(
            $command->user->id,
            $command->service->id,
            $features
        );

        if ($success) {

            // creates 1 event per source
            // sourceId will be 0 to null sources
            foreach ($featuresPerSource as $sourceId => $sourceFeatures) {
                $source = ($sourceId ? $sources[$sourceId] : null);
                $process = $this->getRelatedProcess($command->user->id, $source ? $source : null);

                $event = $this->eventFactory->create('Profile\\Feature\\CreatedBulk', $sourceFeatures, $command->user, $command->credential, $process, $source);
                $this->emitter->emit($event);
            }
        }

        return $success;
    }

    /**
     * Gets the process.
     *
     * @param CommandInterface $command The command
     * 
     * @return App\Entity\Profile\Process
     */
    private function getRelatedProcess(int $userId, $source = null) : Process {
        $event = sprintf('idos:feature.%s.created', $source ? $source->name : 'profile');
        $sourceId = $source ? $source->id : null;

        try {
            if ($source) {
                return $this->processRepository->findOneBySourceId($sourceId);
            }
            
            return $this->processRepository->findLastByUserIdSourceIdAndEvent($userId, $sourceId, $event);
        } catch (NotFound $e) {
            $entity = $this->processRepository->create([
                'name'      => 'idos:verification',
                'user_id'   => $userId,
                'source_id' => $sourceId,
                'event'     => $event
            ]);

            return $this->processRepository->save($entity);
        }
    }

}
