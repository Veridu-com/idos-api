<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Handler\Profile;

use App\Command\Profile\Review\CreateNew;
use App\Command\Profile\Review\UpdateOne;
use App\Command\Profile\Review\UpsertOne;
use App\Entity\Profile\Review as ReviewEntity;
use App\Exception\Create;
use App\Exception\Update;
use App\Exception\Upsert\Profile\ReviewException as UpsertException;
use App\Exception\Validate;
use App\Factory\Event;
use App\Handler\HandlerInterface;
use App\Repository\RepositoryInterface;
use App\Validator\Profile\Review as ReviewValidator;
use Interop\Container\ContainerInterface;
use League\Event\Emitter;
use Respect\Validation\Exceptions\ValidationException;

/**
 * Handles Review commands.
 */
class Review implements HandlerInterface {
    /**
     * Review Repository instance.
     *
     * @var \App\Repository\RepositoryInterface
     */
    private $repository;
    /**
     * Recommendation Repository instance.
     *
     * @var \App\Repository\RepositoryInterface
     */
    private $recommendationRepository;
    /**
     * Review Validator instance.
     *
     * @var \App\Validator\Profile\Review
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

            return new \App\Handler\Profile\Review(
                $repositoryFactory
                    ->create('Profile\Review'),
                $repositoryFactory
                    ->create('Profile\Recommendation'),
                $container
                    ->get('validatorFactory')
                    ->create('Profile\Review'),
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
     * @param \App\Repository\RepositoryInterface $repository
     * @param \App\Repository\RepositoryInterface $recommendationRepository
     * @param \App\Validator\Profile\Review       $validator
     * @param \App\Factory\Event                  $eventFactory
     * @param \League\Event\Emitter               $emitter
     *
     * @return void
     */
    public function __construct(
        RepositoryInterface $repository,
        RepositoryInterface $recommendationRepository,
        ReviewValidator $validator,
        Event $eventFactory,
        Emitter $emitter
    ) {
        $this->repository               = $repository;
        $this->recommendationRepository = $recommendationRepository;
        $this->validator                = $validator;
        $this->eventFactory             = $eventFactory;
        $this->emitter                  = $emitter;
    }

    /**
     * Creates a new review data in the given user.
     *
     * @param \App\Command\Profile\Review\CreateNew $command
     *
     * @see \App\Repository\Profile\DBReview::create
     * @see \App\Repository\Profile\DBReview::save
     *
     * @throws \App\Exception\Validate\Profile\ReviewException
     * @throws \App\Exception\Create\Profile\ReviewException
     *
     * @return \App\Entity\Profile\Review
     */
    public function handleCreateNew(CreateNew $command) : ReviewEntity {
        try {
            $this->validator->assertUser($command->user, 'user');
            $this->validator->assertId($command->gateId, 'gateId');
            $this->validator->assertFlag($command->positive, 'positive');
            $this->validator->assertIdentity($command->identity, 'identity');
        } catch (ValidationException $exception) {
            throw new Validate\Profile\ReviewException(
                $exception->getMessage(),
                400,
                $exception
            );
        }

        $entity = $this->repository->create(
            [
                'user_id'     => $command->user->id,
                'identity_id' => $command->identity->id,
                'gate_id'     => $command->gateId,
                'positive'    => $this->validator->validateFlag($command->positive),
                'created_at'  => time()
            ]
        );

        try {
            $entity = $this->repository->save($entity);

            $event = $this->eventFactory->create('Profile\Review\Created', $entity, $command->identity);
            $this->emitter->emit($event);
        } catch (\Exception $exception) {
            throw new Create\Profile\ReviewException('Error while trying to create a review', 500, $exception);
        }

        return $entity;
    }

    /**
     * Updates a review data from a given user.
     *
     * @param \App\Command\Profile\Review\UpdateOne $command
     *
     * @see \App\Repository\DBReview::findOneByUserIdAndId
     * @see \App\Repository\DBReview::save
     *
     * @throws \App\Exception\Validate\Profile\ReviewException
     * @throws \App\Exception\Update\Profile\ReviewException
     *
     * @return \App\Entity\Profile\Review
     */
    public function handleUpdateOne(UpdateOne $command) : ReviewEntity {
        try {
            $this->validator->assertId($command->id, 'id');
            $this->validator->assertUser($command->user, 'user');
            $this->validator->assertFlag($command->positive, 'positive');
            $this->validator->assertIdentity($command->identity, 'identity');
        } catch (ValidationException $exception) {
            throw new Validate\Profile\ReviewException(
                $exception->getMessage(),
                400,
                $exception
            );
        }

        $review           = $this->repository->find($command->id);
        $review->positive = $command->positive;

        try {
            $review = $this->repository->save($review);
            $event  = $this->eventFactory->create('Profile\Review\Updated', $review, $command->identity);
            $this->emitter->emit($event);
        } catch (\Exception $exception) {
            throw new Update\Profile\ReviewException('Error while trying to update a review', 500, $exception);
        }

        return $review;
    }

    /**
     * Create or update a review from a given user.
     *
     * @param \App\Command\Profile\Review\UpsertOne $command
     *
     * @see \App\Repository\DBReview::findOneByUserIdAndId
     * @see \App\Repository\DBReview::save
     *
     * @throws \App\Exception\Validate\Profile\ReviewException
     * @throws \App\Exception\Update\Profile\ReviewException
     *
     * @return \App\Entity\Profile\Review
     */
    public function handleUpsertOne(UpsertOne $command) : ReviewEntity {
        try {
            $this->validator->assertUser($command->user, 'user');
            $this->validator->assertFlag($command->positive, 'positive');
            $this->validator->assertIdentity($command->identity, 'identity');

            if ((bool) $command->gateId === (bool) $command->recommendationId) {
                throw new ValidationException('A review should belong to strictly one Gate or Review');
            }
        } catch (ValidationException $exception) {
            throw new Validate\Profile\ReviewException(
                $exception->getMessage(),
                400,
                $exception
            );
        }

        if ($command->gateId === null) {
            $recommendation = $this->recommendationRepository->findOne($command->user->id);
        }

        $review = $this->repository->create(
            [
                'user_id'           => $command->user->id,
                'identity_id'       => $command->identity->id,
                'recommendation_id' => $command->recommendationId,
                'gate_id'           => $command->gateId,
                'description'       => $command->description,
                'positive'          => $this->validator->validateFlag($command->positive)
            ]
        );

        $this->repository->beginTransaction();

        try {
            if (isset($command->gateId)) {
                $review = $this->repository->upsert(
                    $review,
                    [
                        'user_id',
                        'gate_id'
                    ],
                    [
                        'positive'    => $review->positive,
                        'description' => $review->description
                    ]
                );
            }

            if (isset($command->recommendationId)) {
                $review = $this->repository->upsert(
                    $review,
                    [
                        'user_id',
                        'recommendation_id'
                    ],
                    [
                        'positive'    => $review->positive,
                        'description' => $review->description
                    ]
                );
            }

            $this->repository->commit();
        } catch (\Exception $exception) {
            $this->repository->rollBack();
            throw new UpsertException($e->getMessage());
        }

        return $review;
    }
}
