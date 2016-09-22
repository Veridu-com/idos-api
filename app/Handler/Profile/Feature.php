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
use App\Entity\Profile\Feature as FeatureEntity;
use App\Exception\Create;
use App\Exception\NotFound;
use App\Exception\Update;
use App\Exception\Validate;
use App\Factory\Event;
use App\Handler\HandlerInterface;
use App\Repository\Profile\FeatureInterface;
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
        FeatureValidator $validator,
        Event $eventFactory,
        Emitter $emitter
    ) {
        $this->repository       = $repository;
        $this->sourceRepository = $sourceRepository;
        $this->validator        = $validator;
        $this->eventFactory     = $eventFactory;
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
                'user_id'    => $command->user->id,
                'source'     => $command->source !== null ? $command->source->name : null,
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

            $event = $this->eventFactory->create('Profile\\Feature\\Created', $feature);
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
            $this->validator->assertService($command->service);
            $this->validator->assertId($command->featureId);
            $this->validator->assertName($command->type);
        } catch (ValidationException $e) {
            throw new Validate\Profile\FeatureException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        $feature = $this->repository->findOneBy([
            'user_id' => $command->user->id,
            'creator' => $command->service->id,
            'id'      => $command->featureId
        ]);

        $feature->type      = $command->type;
        $feature->value     = $command->value;
        $feature->updatedAt = time();

        try {
            $feature = $this->repository->save($feature);
            $feature = $this->repository->hydrateRelations($feature);

            $event = $this->eventFactory->create('Profile\\Feature\\Updated', $feature);
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
                    'user_id'    => $command->user->id,
                    'source'     => $command->source !== null ? $command->source->name : null,
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

            if ($inserting) {
                $event = $this->eventFactory->create('Profile\\Feature\\Created', $feature);
            } else {
                $event = $this->eventFactory->create('Profile\\Feature\\Updated', $feature);
            }

            $this->emitter->emit($event);
        } catch (\Exception $e) {
            throw new NotFound\FeatureException('Error while trying to upsert a feature', 404, $e);
        }

        return $feature;
    }
}
