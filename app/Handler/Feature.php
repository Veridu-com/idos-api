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
use App\Entity\Feature as FeatureEntity;
use App\Event\Feature\Created;
use App\Event\Feature\Deleted;
use App\Event\Feature\DeletedMulti;
use App\Event\Feature\Updated;
use App\Exception\Create;
use App\Exception\NotFound;
use App\Exception\Update;
use App\Exception\Validate;
use App\Repository\FeatureInterface;
use App\Validator\Feature as FeatureValidator;
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
     * @var App\Repository\FeatureInterface
     */
    protected $repository;
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
        FeatureValidator $validator,
        Emitter $emitter
    ) {
        $this->repository = $repository;
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
        try {
            $this->validator->assertLongName($command->name);
            $this->validator->assertId($command->userId);
        } catch (ValidationException $e) {
            throw new Validate\FeatureException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        $feature = $this->repository->create(
            [
                'name'       => $command->name,
                'value'      => $command->value,
                'user_id'    => $command->userId,
                'created_at' => time()
            ]
        );

        try {
            $feature = $this->repository->save($feature);
            $event   = new Created($feature);
            $this->emitter->emit($event);
        } catch (\Exception $e) {
            throw new Create\FeatureException('Error while trying to create a feature', 500, $e);
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
        try {
            $this->validator->assertSlug($command->featureSlug);
            $this->validator->assertId($command->userId);
        } catch (ValidationException $e) {
            throw new Validate\FeatureException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        $feature = $this->repository->findByUserIdAndSlug($command->userId, $command->featureSlug);

        if ($command->value) {
            $feature->value     = $command->value;
            $feature->updatedAt = time();
        }

        try {
            $feature = $this->repository->save($feature);
            $event   = new Updated($feature);
            $this->emitter->emit($event);
        } catch (\Exception $e) {
            throw new Update\FeatureException('Error while trying to update a feature', 500, $e);
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
        try {
            $this->validator->assertId($command->userId);
        } catch (ValidationException $e) {
            throw new Validate\FeatureException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        $deletedFeatures = $this->repository->findByUserId($command->userId);

        $rowsAffected = $this->repository->deleteByUserId($command->userId);

        $event = new DeletedMulti($deletedFeatures);
        $this->emitter->emit($event);

        return $rowsAffected;
    }

    /**
     * Deletes a Feature.
     *
     * @param App\Command\Feature\DeleteOne $command
     *
     * @return void
     */
    public function handleDeleteOne(DeleteOne $command) {
        try {
            $this->validator->assertSlug($command->featureSlug);
            $this->validator->assertId($command->userId);
        } catch (ValidationException $e) {
            throw new Validate\FeatureException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        $feature = $this->repository->findByUserIdAndSlug($command->userId, $command->featureSlug);

        $rowsAffected = $this->repository->delete($feature->id);

        if (! $rowsAffected) {
            throw new NotFound\FeatureException('No features found for deletion', 404);
        }

        $event = new Deleted($feature);
        $this->emitter->emit($event);
    }
}
