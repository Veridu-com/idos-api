<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Controller;

use App\Entity\User;
use App\Factory\Command;
use App\Repository\ScoreInterface;
use App\Repository\AttributeInterface;
use League\Tactician\CommandBus;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Handles requests to /profiles/{userName}/attribute/{attributeName}/score.
 */
class Scores implements ControllerInterface {
    /**
     * Score Repository instance.
     *
     * @var App\Repository\ScoreInterface
     */
    private $repository;
    /**
     * Attribute Repository instance.
     *
     * @var App\Repository\AttributeInterface
     */
    private $attributeRepository;
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
     * @param App\Repository\ScoreInterface $repository
     * @param \League\Tactician\CommandBus   $commandBus
     * @param App\Factory\Command            $commandFactory
     *
     * @return void
     */
    public function __construct(
        ScoreInterface $repository,
        AttributeInterface $attributeRepository,
        CommandBus $commandBus,
        Command $commandFactory
    ) {
        $this->repository     = $repository;
        $this->attributeRepository = $attributeRepository;
        $this->commandBus     = $commandBus;
        $this->commandFactory = $commandFactory;
    }

    /**
     * Retrieve a complete list of the score by a given attribute.
     *
     * @apiEndpointURIFragment string userName usr001
     * @apiEndpointURIFragment string attributeName firstName
     * @apiEndpointParam       query  string   names firstName,lastName
     * @apiEndpointResponse    200    schema/score/listAll.json
     *
     * @param \Psr\ServerRequestInterface $request
     * @param \Psr\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function listAll(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $user     = $request->getAttribute('targetUser');
        $attributeName = $request->getAttribute('attributeName');
        $names    = $request->getQueryParam('names', []);

        if ($names) {
            $names = explode(',', $names);
        }

        $scores = $this->repository->getAllByUserIdAttributeNameAndNames($user->id, $attributeName, $names);

        $body = [
            'data'    => $scores->toArray(),
            'updated' => (
                $scores->isEmpty() ? time() : max($scores->max('updatedAt'), $scores->max('createdAt'))
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
     * Created a new score for a given attribute.
     *
     * @apiEndpointRequiredParam body   string     name  firstName
     * @apiEndpointRequiredParam body   float     value 1.2
     * @apiEndpointResponse 201 schema/score/scoreEntity.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function createNew(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $command = $this->commandFactory->create('Score\\CreateNew');

        $user = $request->getAttribute('targetUser');
        $attributeName = $request->getAttribute('attributeName');

        $command
            ->setParameters($request->getParsedBody())
            ->setParameter('user', $targetUseruser)
            ->setParameter('attribute', $this->attributeRepository->findOneByUserIdAndName($user->id, $attributeName));

        $score = $this->commandBus->handle($command);

        $body = [
            'status' => true,
            'data'   => $score->toArray()
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
     * Updates a score from the given attribute.
     *
     * @apiEndpointURIFragment   string scoreName overall
     * @apiEndpointRequiredParam body   float     value     1.2
     * @apiEndpointResponse      200    schema/score/updateOne.json
     *
     * @param \Psr\ServerRequestInterface $request
     * @param \Psr\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function updateOne(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $command = $this->commandFactory->create('Score\\UpdateOne');

        $user = $request->getAttribute('targetUser');
        $attributeName = $request->getAttribute('attributeName');

        $command
            ->setParameters($request->getParsedBody())
            ->setParameter('user', $user)
            ->setParameter('attribute', $this->attributeRepository->findOneByUserIdAndName($user->id, $attributeName))
            ->setParameter('name', $request->getAttribute('scoreName'));

        $score = $this->commandBus->handle($command);

        $body = [
            'data'    => $score->toArray(),
            'updated' => $score->updated_at
        ];

        $command = $this->commandFactory->create('ResponseDispatch');
        $command
            ->setParameter('request', $request)
            ->setParameter('response', $response)
            ->setParameter('body', $body);

        return $this->commandBus->handle($command);
    }

    /**
     * Retrieves a score from the given attribute.
     *
     * @apiEndpointURIFragment string userName usr001
     * @apiEndpointURIFragment string    attributeName firstName
     * @apiEndpointParam       query  string   scoreName overall
     * @apiEndpointResponse 200 schema/score/scoreEntity.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getOne(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $user     = $request->getAttribute('targetUser');
        $attributeName = $request->getAttribute('attributeName');
        $name     = $request->getAttribute('scoreName');

        $score = $this->repository->findOneByUserIdAttributeNameAndName($user->id, $attributeName, $name);

        $body = [
            'data' => $score->toArray()
        ];

        $command = $this->commandFactory->create('ResponseDispatch');
        $command
            ->setParameter('request', $request)
            ->setParameter('response', $response)
            ->setParameter('body', $body);

        return $this->commandBus->handle($command);
    }

    /**
     * Deletes all scores from a given attribute.
     *
     * @apiEndpointResponse 200 schema/score/deleteAll.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function deleteAll(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $command = $this->commandFactory->create('Score\\DeleteAll');

        $user = $request->getAttribute('targetUser');
        $attributeName = $request->getAttribute('attributeName');

        $command
            ->setParameter('user', $user)
            ->setParameter('attribute', $this->attributeRepository->findOneByUserIdAndName($user->id, $attributeName));

        $body = [
            'deleted' => $this->commandBus->handle($command)
        ];

        $command = $this->commandFactory->create('ResponseDispatch');
        $command
            ->setParameter('request', $request)
            ->setParameter('response', $response)
            ->setParameter('body', $body);

        return $this->commandBus->handle($command);
    }

    /**
     * Deletes a score from a given attribute.
     *
     * @apiEndpointURIFragment string userName usr001
     * @apiEndpointURIFragment string    attributeName firstName
     * @apiEndpointURIFragment string scoreName overall
     * @apiEndpointResponse 200 schema/score/deleteOne.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function deleteOne(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $command = $this->commandFactory->create('Score\\DeleteOne');

        $user = $request->getAttribute('targetUser');
        $attributeName = $request->getAttribute('attributeName');

        $command
            ->setParameter('user', $user)
            ->setParameter('attribute', $this->attributeRepository->findOneByUserIdAndName($user->id, $attributeName))
            ->setParameter('name', $request->getAttribute('scoreName'));

        $deleted = $this->commandBus->handle($command);
        $body    = [
            'status' => $deleted === 1
        ];

        $statusCode = $body['status'] ? 200 : 404;

        $command = $this->commandFactory->create('ResponseDispatch');
        $command
            ->setParameter('statusCode', $statusCode)
            ->setParameter('request', $request)
            ->setParameter('response', $response)
            ->setParameter('body', $body);

        return $this->commandBus->handle($command);
    }
}
