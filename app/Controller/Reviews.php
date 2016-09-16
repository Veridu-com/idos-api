<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Controller;

use App\Entity\User;
use App\Factory\Command;
use App\Repository\ReviewInterface;
use League\Tactician\CommandBus;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Handles requests to /profiles/{userName}/review.
 */
class Reviews implements ControllerInterface {
    /**
     * Review Repository instance.
     *
     * @var App\Repository\ReviewInterface
     */
    private $repository;
    /**
     * Command Bus instance.
     *
     * @var \League\Tactician\CommandBus
     */
    private $commandBus;
    /**
     * Command Factory instance.
     *
     * @var App\Factory\Command
     */
    private $commandFactory;

    /**
     * Class constructor.
     *
     * @param App\Repository\ReviewInterface $repository
     * @param \League\Tactician\CommandBus   $commandBus
     * @param App\Factory\Command            $commandFactory
     *
     * @return void
     */
    public function __construct(
        ReviewInterface $repository,
        CommandBus $commandBus,
        Command $commandFactory
    ) {
        $this->repository     = $repository;
        $this->commandBus     = $commandBus;
        $this->commandFactory = $commandFactory;
    }

    /**
     * Retrieve a complete list of reviews, given an user and an warning.
     *
     * @apiEndpointURIFragment string userName usr001
     * @apiEndpointParam query string warnings WRONG FORMAT HERE FIXME
     * @apiEndpointResponse 200 schema/review/listAll.json
     *
     * @param \Psr\ServerRequestInterface $request
     * @param \Psr\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function listAll(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $user       = $request->getAttribute('targetUser');
        $warningIds = $request->getQueryParam('warnings', []);

        if ($warningIds) {
            $warningIds = explode(',', $warningIds);
        }

        $reviews = $this->repository->getAllByUserIdAndWarningIds($user->id, $warningIds);

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
     * Created a new review data for a given source.
     *
     * @apiEndpointResponse 201 schema/review/reviewEntity.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function createNew(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $command = $this->commandFactory->create('Review\\CreateNew');

        $command
            ->setParameters($request->getParsedBody())
            ->setParameter('user', $request->getAttribute('targetUser'));

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
     * @apiEndpointRequiredParam body string value WRONG FORMAT HERE FIXME
     * @apiEndpointResponse 200 schema/review/updateOne.json
     *
     * @param \Psr\ServerRequestInterface $request
     * @param \Psr\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function updateOne(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $command = $this->commandFactory->create('Review\\UpdateOne');
        $command
            ->setParameters($request->getParsedBody())
            ->setParameter('user', $request->getAttribute('targetUser'))
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

    /**
     * Retrieves a review data from the given source.
     *
     * @apiEndpointURIFragment string userName usr001
     * @apiEndpointParam query string reviewId 12345
     * @apiEndpointResponse 200 schema/review/reviewEntity.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getOne(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $user = $request->getAttribute('targetUser');

        $review = $this->repository->findOneByUserIdAndId($user->id, (int) $request->getAttribute('decodedReviewId'));

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
}
