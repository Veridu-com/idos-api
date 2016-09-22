<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Handler\Profile;

use App\Command\Profile\Review\CreateNew;
use App\Command\Profile\Review\UpdateOne;
use App\Entity\Profile\Review as ReviewEntity;
use App\Exception\Create;
use App\Exception\Update;
use App\Exception\Validate;
use App\Factory\Event;
use App\Handler\HandlerInterface;
use App\Repository\Profile\ReviewInterface;
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
     * @var App\Repository\Profile\ReviewInterface
     */
    private $repository;
    /**
     * Review Validator instance.
     *
     * @var App\Validator\Profile\Review
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
     * @var \League\Event\Emitter
     */
    private $emitter;

    /**
     * {@inheritdoc}
     */
    public static function register(ContainerInterface $container) {
        $container[self::class] = function (ContainerInterface $container) {
            return new \App\Handler\Profile\Review(
                $container
                    ->get('repositoryFactory')
                    ->create('Profile\Review'),
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
     * @param App\Repository\ReviewInterface $repository
     * @param App\Validator\Review           $validator
     * @param App\Factory\Event              $eventFactory
     * @param \League\Event\Emitter          $emitter
     *
     * @return void
     */
    public function __construct(
        ReviewInterface $repository,
        ReviewValidator $validator,
        Event $eventFactory,
        Emitter $emitter
    ) {
        $this->repository   = $repository;
        $this->validator    = $validator;
        $this->eventFactory = $eventFactory;
        $this->emitter      = $emitter;
    }

    /**
     * Creates a new review data in the given user.
     *
     * @param App\Command\Profile\Review\CreateNew $command
     *
     * @see App\Repository\Profile\DBReview::create
     * @see App\Repository\Profile\DBReview::save
     *
     * @throws App\Exception\Validate\ReviewException
     * @throws App\Exception\Create\ReviewException
     *
     * @return App\Entity\Review
     */
    public function handleCreateNew(CreateNew $command) : ReviewEntity {
        try {
            $this->validator->assertUser($command->user);
            $this->validator->assertId($command->warningId);
            $this->validator->assertFlag($command->positive);
            $this->validator->assertIdentity($command->identity);
        } catch (ValidationException $e) {
            throw new Validate\Profile\ReviewException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        $review = $this->repository->create(
            [
                'user_id'     => $command->user->id,
                'identity_id' => $command->identity->id,
                'warning_id'  => $command->warningId,
                'positive'    => $this->validator->validateFlag($command->positive),
                'created_at'  => time()
            ]
        );

        try {
            $review = $this->repository->save($review);
            $event  = $this->eventFactory->create('Profile\\Review\\Created', $review, $command->identity);
            $this->emitter->emit($event);
        } catch (\Exception $e) {
            throw new Create\Profile\ReviewException('Error while trying to create a review', 500, $e);
        }

        return $review;
    }

    /**
     * Updates a review data from a given user.
     *
     * @param App\Command\Profile\Review\UpdateOne $command
     *
     * @see App\Repository\DBReview::findOneByUserIdAndId
     * @see App\Repository\DBReview::save
     *
     * @throws App\Exception\Validate\ReviewException
     * @throws App\Exception\Update\ReviewException
     *
     * @return App\Entity\Review
     */
    public function handleUpdateOne(UpdateOne $command) : ReviewEntity {
        try {
            $this->validator->assertId($command->id);
            $this->validator->assertUser($command->user);
            $this->validator->assertFlag($command->positive);
            $this->validator->assertIdentity($command->identity);
        } catch (ValidationException $e) {
            throw new Validate\Profile\ReviewException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        $review           = $this->repository->findOneByUserIdAndIdAndIdentityId($command->user->id, $command->id, $command->identity->id);
        $review->positive = $command->positive;

        try {
            $review = $this->repository->save($review);
            $event  = $this->eventFactory->create('Profile\\Review\\Updated', $review);
            $this->emitter->emit($event);
        } catch (\Exception $e) {
            throw new Update\Profile\ReviewException('Error while trying to update a review', 500, $e);
        }

        return $review;
    }
}
