<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Controller\Profile;

use App\Controller\ControllerInterface;
use App\Entity\User;
use App\Factory\Command;
use App\Repository\Profile\ReviewInterface;
use App\Repository\UserInterface;
use League\Tactician\CommandBus;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Handles requests to companies/{companySlug}/profiles/{userId}/flags/{flagId}/reviews.
 */
class Reviews implements ControllerInterface {
    /**
     * Review Repository instance.
     *
     * @var \App\Repository\Profile\ReviewInterface
     */
    private $repository;
    /**
     * User Repository instance.
     *
     * @var \App\Repository\UserInterface
     */
    private $userRepository;
    /**
     * Command Bus instance.
     *
     * @var \League\Tactician\CommandBus
     */
    private $commandBus;
    /**
     * Command Factory instance.
     *
     * @var \App\Factory\Command
     */
    private $commandFactory;

    /**
     * Class constructor.
     *
     * @param \App\Repository\Profile\ReviewInterface $repository
     * @param \League\Tactician\CommandBus            $commandBus
     * @param \App\Factory\Command                    $commandFactory
     *
     * @return void
     */
    public function __construct(
        ReviewInterface $repository,
        UserInterface $userRepository,
        CommandBus $commandBus,
        Command $commandFactory
    ) {
        $this->repository     = $repository;
        $this->userRepository = $userRepository;
        $this->commandBus     = $commandBus;
        $this->commandFactory = $commandFactory;
    }

    /**
     * Retrieve a complete list of reviews, given an user and an flag.
     *
     * @apiEndpointResponse 200 schema/review/listAll.json
     *
     * @param \Psr\ServerRequestInterface $request
     * @param \Psr\ResponseInterface      $response
     *
     * @see \App\Repository\Profile\DBReview::getAllByUserIdAndFlagIdsAndIdentity
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function listAll(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $user       = $this->userRepository->find($request->getAttribute('decodedUserId'));
        $identity   = $request->getAttribute('identity');

        $reviews = $this->repository->getByUserIdAndIdentityId($identity->id, $user->id, $request->getQueryParams());

        $body = [
            'data'    => $reviews->toArray(),
            'updated' => (
                $reviews->isEmpty() ? time() : max($reviews->max('updatedAt'), $reviews->max('createdAt'))
            )
        ];

        $command = $this->commandFactory->create('ResponseDispatch');
        $command
            ->setParameter('request', $request)
            ->setParameter('response', $response)
            ->setParameter('body', $body);

        return $this->commandBus->handle($command);
    }

    /**
     * Retrieves a review data from the given source.
     *
     * @apiEndpointResponse 200 schema/review/getOne.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getOne(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $user     = $this->userRepository->find($request->getAttribute('decodedUserId'));
        $identity = $request->getAttribute('identity');
        $reviewId = (int) $request->getAttribute('decodedReviewId');

        $review = $this->repository->findOne($reviewId, $identity->id, $user->id);

        $body = [
            'data' => $review->toArray()
        ];

        $command = $this->commandFactory->create('ResponseDispatch');
        $command
            ->setParameter('request', $request)
            ->setParameter('response', $response)
            ->setParameter('body', $body);

        return $this->commandBus->handle($command);
    }

    /**
     * Created a new review data for a given source.
     *
     * @apiEndpointResponse 201 schema/review/createNew.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @see \App\Handler\Profile\Review::handleCreateNew
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function createNew(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $command  = $this->commandFactory->create('Profile\\Review\\CreateNew');
        $user     = $this->userRepository->find($request->getAttribute('decodedUserId'));
        $identity = $request->getAttribute('identity');

        $command
            ->setParameters($request->getParsedBody() ?: [])
            ->setParameter('user', $user)
            ->setParameter('identity', $identity);

        $review = $this->commandBus->handle($command);

        $body = [
            'status' => true,
            'data'   => $review->toArray()
        ];

        $command = $this->commandFactory->create('ResponseDispatch');
        $command
            ->setParameter('statusCode', 201)
            ->setParameter('request', $request)
            ->setParameter('response', $response)
            ->setParameter('body', $body);

        return $this->commandBus->handle($command);
    }

    /**
     * Updates a review data from the given source.
     *
     * @apiEndpointRequiredParam body boolean positive false
     * @apiEndpointResponse 200 schema/review/updateOne.json
     *
     * @param \Psr\ServerRequestInterface $request
     * @param \Psr\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function updateOne(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $user     = $this->userRepository->find($request->getAttribute('decodedUserId'));
        $identity = $request->getAttribute('identity');

        $command = $this->commandFactory->create('Profile\\Review\\UpdateOne');
        $command
            ->setParameters($request->getParsedBody() ?: [])
            ->setParameter('user', $user)
            ->setParameter('identity', $identity)
            ->setParameter('id', (int) $request->getAttribute('decodedReviewId'));

        $review = $this->commandBus->handle($command);

        $body = [
            'data'    => $review->toArray(),
            'updated' => $review->updatedAt
        ];

        $command = $this->commandFactory->create('ResponseDispatch');
        $command
            ->setParameter('request', $request)
            ->setParameter('response', $response)
            ->setParameter('body', $body);

        return $this->commandBus->handle($command);
    }
}
