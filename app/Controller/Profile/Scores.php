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
use App\Repository\Profile\AttributeInterface;
use App\Repository\Profile\ScoreInterface;
use League\Tactician\CommandBus;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Handles requests to /profiles/{userName}/scores and /profiles/{userName}/scores/{scoreName}.
 */
class Scores implements ControllerInterface {
    /**
     * Score Repository instance.
     *
     * @var \App\Repository\Profile\ScoreInterface
     */
    private $repository;
    /**
     * Attribute Repository instance.
     *
     * @var \App\Repository\Profile\AttributeInterface
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
     * @var \App\Factory\Command
     */
    private $commandFactory;

    /**
     * Class constructor.
     *
     * @param \App\Repository\Profile\ScoreInterface $repository
     * @param \League\Tactician\CommandBus           $commandBus
     * @param \App\Factory\Command                   $commandFactory
     *
     * @return void
     */
    public function __construct(
        ScoreInterface $repository,
        AttributeInterface $attributeRepository,
        CommandBus $commandBus,
        Command $commandFactory
    ) {
        $this->repository          = $repository;
        $this->attributeRepository = $attributeRepository;
        $this->commandBus          = $commandBus;
        $this->commandFactory      = $commandFactory;
    }

    /**
     * Retrieve a complete list of the score by a given attribute.
     *
     * @apiEndpointParam       query  string   names firstName,lastName
     * @apiEndpointResponse 200 schema/score/listAll.json
     *
     * @see \App\Repository\Profile\DBScore::findBy
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function listAll(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $user = $request->getAttribute('targetUser');

        $entities = $this->repository->getByUserId($user->id, $request->getQueryParams());

        $body = [
            'data'    => $entities->toArray(),
            'updated' => (
                $entities->isEmpty() ? time() : max($entities->max('updatedAt'), $entities->max('createdAt'))
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
     * Retrieves a score from the given attribute.
     *
     * @apiEndpointResponse 200 schema/score/getOne.json
     *
     * @see \App\Repository\Profile\DBScore::findOneByName
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getOne(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $user    = $request->getAttribute('targetUser');
        $service = $request->getAttribute('service');
        $name    = $request->getAttribute('scoreName');

        $score = $this->repository->findOne($name, $service->id, $user->id);

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
     * Created a new score for a given attribute.
     *
     * @apiEndpointRequiredParam body   string     attribute  firstName Score attribute
     * @apiEndpointRequiredParam body   string     name  overall Score name
     * @apiEndpointRequiredParam body   float     value 0.2 Score value
     * @apiEndpointResponse 201 schema/score/scoreEntity.json
     *
     * @see \App\Handler\Profile\Score::handleCreateNew
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function createNew(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $user    = $request->getAttribute('targetUser');
        $service = $request->getAttribute('service');
        $credential = $request->getAttribute('credential');

        $command = $this->commandFactory->create('Profile\\Score\\CreateNew');
        $command
            ->setParameters($request->getParsedBody() ?: [])
            ->setParameter('actor', $credential)
            ->setParameter('user', $user)
            ->setParameter('service', $service);

        $entity = $this->commandBus->handle($command);

        $body = [
            'status' => true,
            'data'   => $entity->toArray()
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
     * @apiEndpointRequiredParam body   string     attribute  firstName Score attribute
     * @apiEndpointRequiredParam body   string     name  overall Score name
     * @apiEndpointRequiredParam body   float     value 0.2 Score value
     * @apiEndpointResponse 200 schema/score/updateOne.json
     *
     * @see \App\Handler\Profile\Score::handleUpdateOne
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function updateOne(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $user    = $request->getAttribute('targetUser');
        $service = $request->getAttribute('service');
        $name    = $request->getAttribute('scoreName');
        $credential = $request->getAttribute('credential');

        $command = $this->commandFactory->create('Profile\\Score\\UpdateOne');
        $command
            ->setParameters($request->getParsedBody() ?: [])
            ->setParameter('actor', $credential)
            ->setParameter('user', $user)
            ->setParameter('service', $service)
            ->setParameter('name', $name);

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
     * Created a new score for a given attribute.
     *
     * @apiEndpointRequiredParam body   string     attribute  firstName Score attribute
     * @apiEndpointRequiredParam body   string     name  overall Score name
     * @apiEndpointRequiredParam body   float     value 0.2 Score value
     * @apiEndpointResponse 201 schema/score/createNew.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function upsert(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $user    = $request->getAttribute('targetUser');
        $service = $request->getAttribute('service');
        $credential = $request->getAttribute('credential');

        $command = $this->commandFactory->create('Profile\\Score\\Upsert');
        $command
            ->setParameters($request->getParsedBody() ?: [])
            ->setParameter('actor', $credential)
            ->setParameter('user', $user)
            ->setParameter('service', $service);

        $entity = $this->commandBus->handle($command);

        $body = [
            'status' => true,
            'data'   => $entity->toArray()
        ];

        $command = $this->commandFactory->create('ResponseDispatch');
        $command
            ->setParameter('statusCode', isset($entity->updatedAt) ? 200 : 201)
            ->setParameter('request', $request)
            ->setParameter('response', $response)
            ->setParameter('body', $body);

        return $this->commandBus->handle($command);
    }

    /**
     * Deletes a score from a given attribute.
     *
     * @apiEndpointResponse 200 schema/score/deleteOne.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @see \App\Handler\Profile\Score::handleDeleteOne
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function deleteOne(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $user    = $request->getAttribute('targetUser');
        $service = $request->getAttribute('service');
        $name    = $request->getAttribute('scoreName');
        $credential = $request->getAttribute('credential');

        $command = $this->commandFactory->create('Profile\\Score\\DeleteOne');
        $command
            ->setParameter('actor', $credential)
            ->setParameter('user', $user)
            ->setParameter('service', $service)
            ->setParameter('name', $name);

        $this->commandBus->handle($command);
        $body = [
            'status' => true
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
     * @see \App\Handler\Profile\Score::handleDeleteAll
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function deleteAll(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $user    = $request->getAttribute('targetUser');
        $service = $request->getAttribute('service');
        $credential = $request->getAttribute('credential');

        $command = $this->commandFactory->create('Profile\\Score\\DeleteAll');
        $command
            ->setParameter('actor', $credential)
            ->setParameter('user', $user)
            ->setParameter('service', $service)
            ->setParameter('queryParams', $request->getQueryParams());

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
}
