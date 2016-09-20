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
use App\Entity\Profile\Feature as FeatureEntity;
use App\Event\Profile\Feature\Created;
use App\Event\Profile\Feature\Deleted;
use App\Event\Profile\Feature\DeletedMulti;
use App\Event\Profile\Feature\Updated;
use App\Exception\Create;
use App\Exception\NotFound;
use App\Exception\Update;
use App\Exception\Validate;
use App\Repository\Profile\FeatureInterface;
use App\Validator\Profile\Feature as FeatureValidator;
use Interop\Container\ContainerInterface;
use League\Event\Emitter;
use Respect\Validation\Exceptions\ValidationException;
use App\Handler\HandlerInterface;

/**
 * Handles Feature commands.
 */
class Feature implements HandlerInterface {
    /**
     * Feature Repository instance.
     *
     * @var App\Repository\Profile\FeatureInterface
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
     * @var App\Validator\Profile\Feature
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
            return new \App\Handler\Profile\Feature(
                $container
                    ->get('repositoryFactory')
                    ->create('Profile\Feature'),
                $container
                    ->get('repositoryFactory')
                    ->create('Source'),
                $container
                    ->get('validatorFactory')
                    ->create('Profile\Feature'),
                $container
                    ->get('eventEmitter')
            );
        };
    }

    /**
     * Class constructor.
     *
     * @param App\Repository\Profile\FeatureInterface $repository
     * @param App\Validator\Profile\Feature           $validator
     *
     * @return void
     */
    public function __construct(
        FeatureInterface $repository,
        SourceInterface $sourceRepository,
        FeatureValidator $validator,
        Emitter $emitter
    ) {
        $this->repository       = $repository;
        $this->sourceRepository = $sourceRepository;
        $this->validator        = $validator;
        $this->emitter          = $emitter;
    }

    /**
     * Creates a feature.
     *
     * @param App\Command\Profile\Feature\CreateNew $command
     *
     * @see App\Repository\DBFeature::save
     *
     * @throws App\Exception\Validate\FeatureException
     * @throws App\Exception\Create\FeatureException
     *
     * @return App\Entity\Feature
     */
    public function handleCreateNew(CreateNew $command) : FeatureEntity {
        try {
            $this->validator->assertUser($command->user);
            $this->validator->assertService($command->service);
            $this->validator->assertLongName($command->name);
            $this->validator->assertName($command->type);
            //$this->validator->assertValue($command->value);

            if ($command->source !== null) {
                $this->validator->assertSource($command->source);
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
                'user_id'       => $command->user->id,
                'source'        => $command->source !== null ? $command->source->name : null,
                'name'          => $command->name,
                'creator'       => $command->service->id,
                'type'          => $command->type,
                'value'         => $command->value,
                'created_at'    => time()
            ]
        );

        try {
            $feature = $this->repository->save($feature);
            $feature = $this->repository->hydrateRelations($feature);

            $event = new Created($feature);
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
     * @throws App\Exception\Validate\FeatureException
     * @throws App\Exception\Update\FeatureException
     *
     * @return App\Entity\Feature
     */
    public function handleUpdateOne(UpdateOne $command) : FeatureEntity {
        try {
            $this->validator->assertUser($command->user);
            $this->validator->assertSource($command->source);
            $this->validator->assertService($command->service);
            $this->validator->assertId($command->featureId);
            $this->validator->assertName($command->type);
            //$this->validator->assertValue($command->value);
        } catch (ValidationException $e) {
            throw new Validate\Profile\FeatureException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        $feature = $this->repository->findOneById($command->user->id, $command->source->name, $command->service->id, $command->featureId);

        $feature->type      = $command->type;
        $feature->value     = $command->value;
        $feature->updatedAt = time();

        try {
            $feature = $this->repository->save($feature);
            $feature = $this->repository->hydrateRelations($feature);

            $event = new Updated($feature);
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
     * @throws App\Exception\Validate\FeatureException
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
            ], $command->queryParams
        );

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
     * @param App\Command\Profile\Feature\DeleteOne $command
     *
     * @see App\Repository\DBFeature::findByUserIdAndSlug
     * @see App\Repository\DBFeature::delete
     *
     * @throws App\Exception\Validate\FeatureException
     * @throws App\Exception\NotFound\FeatureException
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

        $event = new Deleted($feature);
        $this->emitter->emit($event);

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

        if ($command->source !== null) {
            $this->validator->assertSource($command->source);
        }

        $feature   = null;
        $inserting = false;
        try {
            $feature = $this->repository->findOneByName($command->user->id, $command->source !== null ? $command->source->name : null, $command->service->id, $command->name);

            $feature->type      = $command->type;
            $feature->value     = $command->value;
            $feature->updatedAt = time();
        } catch (NotFound $e) {
            $inserting = true;

            $feature = $this->repository->create(
                [
                    'user_id'       => $command->user->id,
                    'source'        => $command->source !== null ? $command->source->name : null,
                    'name'          => $command->name,
                    'creator'       => $command->service->id,
                    'type'          => $command->type,
                    'value'         => $command->value,
                    'created_at'    => time()
                ]
            );
        }

        try {
            $feature = $this->repository->save($feature);
            $feature = $this->repository->hydrateRelations($feature);

            if ($inserting) {
                $event = new Created($feature);
            } else {
                $event = new Updated($feature);
            }

            $this->emitter->emit($event);
        } catch (\Exception $e) {
            throw new NotFound\FeatureException('Error while trying to upsert a feature', 404);
        }

        return $feature;
    }
}
