<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Handler;

use App\Command\Review\CreateNew;
use App\Command\Review\UpdateOne;
use App\Entity\Review as ReviewEntity;
use App\Event\Review\Created;
use App\Event\Review\Updated;
use App\Repository\ReviewInterface;
use App\Validator\Review as ReviewValidator;
use Interop\Container\ContainerInterface;
use League\Event\Emitter;

/**
 * Handles Review commands.
 */
class Review implements HandlerInterface {
    /**
     * Review Repository instance.
     *
     * @var App\Repository\ReviewInterface
     */
    protected $repository;
    /**
     * Review Validator instance.
     *
     * @var App\Validator\Review
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
            return new \App\Handler\Review(
                $container
                    ->get('repositoryFactory')
                    ->create('Review'),
                $container
                    ->get('validatorFactory')
                    ->create('Review'),
                $container
                    ->get('eventEmitter')
            );
        };
    }

    /**
     * Class constructor.
     *
     * @param App\Repository\ReviewInterface $repository
     * @param App\Validator\Review           $validator
     *
     * @return void
     */
    public function __construct(
        ReviewInterface $repository,
        ReviewValidator $validator,
        Emitter $emitter
    ) {
        $this->repository = $repository;
        $this->validator  = $validator;
        $this->emitter    = $emitter;
    }

    /**
     * Creates a new review data in the given user.
     *
     * @param App\Command\Review\CreateNew $command
     *
     * @return App\Entity\Review
     */
    public function handleCreateNew(CreateNew $command) : ReviewEntity {
        $this->validator->assertUser($command->user);
        $this->validator->assertId($command->warningId);
        $this->validator->assertFlag($command->positive);

        $review = $this->repository->create(
            [
            'user_id'    => $command->user->id,
            'warning_id' => $command->warningId,
            'positive'   => $command->positive,
            'created_at' => time()
            ]
        );

        try {
            $review = $this->repository->save($review);
            $event  = new Created($review);
            $this->emitter->emit($event);
        } catch (\Exception $e) {
            throw new AppException('Error while creating an review');
        }

        return $review;
    }

    /**
     * Updates a review data from a given user.
     *
     * @param App\Command\Review\UpdateOne $command
     *
     * @return App\Entity\Review
     */
    public function handleUpdateOne(UpdateOne $command) : ReviewEntity {
        $this->validator->assertId($command->id);
        $this->validator->assertUser($command->user);
        $this->validator->assertFlag($command->positive);

        $review           = $this->repository->findOneByUserIdAndId($command->user->id, $command->id);
        $review->positive = $command->positive;

        try {
            $review = $this->repository->save($review);
            $event  = new Updated($review);
            $this->emitter->emit($event);
        } catch (\Exception $e) {
            throw new AppException('Error while updating review');
        }

        return $review;
    }
}
