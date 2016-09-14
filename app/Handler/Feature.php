<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Handler;

use App\Command\Feature\CreateNew;
use App\Command\Feature\DeleteAll;
use App\Command\Feature\DeleteOne;
use App\Command\Feature\UpdateOne;
use App\Command\Feature\Upsert;
use App\Entity\Feature as FeatureEntity;
use App\Event\Feature\Created;
use App\Event\Feature\Deleted;
use App\Event\Feature\DeletedMulti;
use App\Event\Feature\Updated;
use App\Exception\AppException;
use App\Exception\NotFound;
use Illuminate\Database\QueryException;
use App\Repository\FeatureInterface;
use App\Repository\SourceInterface;
use App\Validator\Feature as FeatureValidator;
use Interop\Container\ContainerInterface;
use League\Event\Emitter;

/**
 * Handles Feature commands.
 */
class Feature implements HandlerInterface {
    /**
     * Feature Repository instance.
     *
     * @var App\Repository\FeatureInterface
     */
    protected $repository;

    /**
     * Source Repository instance.
     *
     * @var App\Repository\FeatureInterface
     */
    protected $sourceRepository;

    /**
     * Feature Validator instance.
     *
     * @var App\Validator\Feature
     */
    protected $validator;

    /**
     * Event emitter instance.
     *
     * @var League\Event\Emitter
     */
    protected $emitter;

    /**
     * {@inheritdoc}
     */
    public static function register(ContainerInterface $container) {
        $container[self::class] = function (ContainerInterface $container) {
            return new \App\Handler\Feature(
                $container
                    ->get('repositoryFactory')
                    ->create('Feature'),
                $container
                    ->get('repositoryFactory')
                    ->create('Source'),
                $container
                    ->get('validatorFactory')
                    ->create('Feature'),
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
     *
     * @return void
     */
    public function __construct(
        FeatureInterface $repository,
        SourceInterface $sourceRepository,
        FeatureValidator $validator,
        Emitter $emitter
    ) {
        $this->repository = $repository;
        $this->sourceRepository = $sourceRepository;
        $this->validator  = $validator;
        $this->emitter    = $emitter;
    }

    /**
     * Creates a feature.
     *
     * @param App\Command\Feature\CreateNew $command
     *
     * @return App\Entity\Feature
     */
    public function handleCreateNew(CreateNew $command) : FeatureEntity {
        $this->validator->assertUser($command->user);
        $this->validator->assertService($command->service);
        $this->validator->assertLongName($command->name);
        $this->validator->assertName($command->type);
        //$this->validator->assertValue($command->value);
        
        if ($command->source !== null) {
            $this->validator->assertSource($command->source);
        }

        $feature = $this->repository->create(
            [
                'user_id'    => $command->user->id,
                'source_id'    => $command->source !== null ? $command->source->id : null,
                'name'       => $command->name,
                'creator'       => $command->service->id,
                'type'       => $command->type,
                'value'      => $command->value,
                'created_at' => time()
            ]
        );

        try {
            $feature = $this->repository->save($feature);
            $feature = $this->repository->hydrateRelations($feature);

            $event   = new Created($feature);
            $this->emitter->emit($event);
        } catch (\Exception $exception) {
            throw new AppException('Error while creating feature');
        }

        return $feature;
    }

    /**
     * Updates a Feature.
     *
     * @param App\Command\Feature\UpdateOne $command
     *
     * @return App\Entity\Feature
     */
    public function handleUpdateOne(UpdateOne $command) : FeatureEntity {
        $this->validator->assertUser($command->user);
        $this->validator->assertSource($command->source);
        $this->validator->assertService($command->service);
        $this->validator->assertId($command->featureId);
        $this->validator->assertName($command->type);
        //$this->validator->assertValue($command->value);

        $feature = $this->repository->findOneById($command->user->id, $command->source->id, $command->service->id, $command->featureId);

        $feature->type      = $command->type;
        $feature->value     = $command->value;
        $feature->updatedAt = time();

        try {
            $feature = $this->repository->save($feature);
            $feature = $this->repository->hydrateRelations($feature);

            $event   = new Updated($feature);
            $this->emitter->emit($event);
        } catch (\Exception $exception) {
            throw new AppException(
                'Error while updating feature'
            );
        }

        return $feature;
    }

    /**
     * Deletes all settings ($command->userId).
     *
     * @param App\Command\Feature\DeleteAll $command
     *
     * @return int
     */
    public function handleDeleteAll(DeleteAll $command) : int {
        $this->validator->assertUser($command->user);
        $this->validator->assertService($command->service);
        $this->validator->assertArray($command->queryParams);

        $deletedFeatures = $this->repository->findBy([
            'user_id' => $command->user->id,
            'creator' => $command->service->id
        ], $command->queryParams);

        $affectedRows = 0;

        foreach ($deletedFeatures as $deletedFeature) {
            $affectedRows += $this->repository->delete($deletedFeature->id);
        }

        $event = new DeletedMulti($deletedFeatures);
        $this->emitter->emit($event);

        return $affectedRows;
    }

    /**
     * Deletes a Feature.
     *
     * @param App\Command\Feature\DeleteOne $command
     *
     * @return int
     */
    public function handleDeleteOne(DeleteOne $command) : int {
        $this->validator->assertUser($command->user);
        $this->validator->assertService($command->service);
        $this->validator->assertId($command->featureId);

        $feature = $this->repository->findOneBy([
            'user_id' => $command->user->id,
            'creator' => $command->service->id,
            'id' => $command->featureId
        ]);

        if ($feature->sourceId !== null && $feature->sourceId !== $command->user->id) {
            throw new NotFound();
        }

        $affectedRows = $this->repository->delete($feature->id);

        if ($affectedRows) {
            $event = new Deleted($feature);
            $this->emitter->emit($event);
        }

        return $affectedRows;
    }

    /**
     * Creates or update a feature.
     *
     * @param App\Command\Feature\Upsert $command
     *
     * @return App\Entity\Feature
     */
    public function handleUpsert(Upsert $command) : FeatureEntity {
        $this->validator->assertUser($command->user);
        $this->validator->assertService($command->service);
        $this->validator->assertLongName($command->name);
        $this->validator->assertName($command->type);
        //$this->validator->assertValue($command->value);
        
        if ($command->source !== null) {
            $this->validator->assertSource($command->source);
        }

        $inserting = false;
        try {
            $feature = $this->repository->create(
                [
                    'user_id'    => $command->user->id,
                    'source_id'    => $command->source !== null ? $command->source->id : null,
                    'name'       => $command->name,
                    'creator'       => $command->service->id,
                    'type'       => $command->type,
                    'value'      => $command->value,
                    'created_at' => time()
                ]
            );

            $feature = $this->repository->save($feature);
            $feature = $this->repository->hydrateRelations($feature);

            $inserting = true;
        } catch (QueryException $e) {
            $feature = $this->repository->findOneByName($command->user->id, $command->source !== null ? $command->source->id : 0, $command->service->id, $command->name);
            $feature->type = $command->type;
            $feature->value = $command->value;

            $feature = $this->repository->save($feature);
            $feature = $this->repository->hydrateRelations($feature);
        } catch (\Exception $e) {
            throw new AppException('Error while upserting feature.');
        }

        if ($inserting) {
            $event   = new Created($feature);
        } else {
            $event   = new Updated($feature);
        }

        $this->emitter->emit($event);

        return $feature;
    }
}
