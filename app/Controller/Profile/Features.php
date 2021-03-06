<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Controller\Profile;

use App\Controller\ControllerInterface;
use App\Factory\Command;
use App\Repository\RepositoryInterface;
use League\Tactician\CommandBus;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Handles requests to /profiles/{userName}/features and /profiles/{userName}/features/{featureId}.
 */
class Features implements ControllerInterface {
    /**
     * Setting Repository instance.
     *
     * @var \App\Repository\RepositoryInterface
     */
    private $repository;
    /**
     * Source Repository instance.
     *
     * @var \App\Repository\RepositoryInterface
     */
    private $sourceRepository;
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
     * @param \App\Repository\RepositoryInterface $repository
     * @param \App\Repository\RepositoryInterface $sourceRepository
     * @param \League\Tactician\CommandBus        $commandBus
     * @param \App\Factory\Command                $commandFactory
     *
     * @return void
     */
    public function __construct(
        RepositoryInterface $repository,
        RepositoryInterface $sourceRepository,
        CommandBus $commandBus,
        Command $commandFactory
    ) {
        $this->repository       = $repository;
        $this->sourceRepository = $sourceRepository;
        $this->commandBus       = $commandBus;
        $this->commandFactory   = $commandFactory;
    }

    /**
     * Lists all Features that belongs to the given user.
     *
     * @apiEndpointParam query int page 10|1 Current page
     * @apiEndpointResponse 200 schema/feature/listAll.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @see \App\Repository\DBFeature::getAllByUserId
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function listAll(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $user = $request->getAttribute('targetUser');

        $entities = $this->repository->getByUserId($user->id, $request->getQueryParams());

        $body = [
            'data'    => $entities->toArray(),
            'updated' => ($entities->isEmpty() ? null : max($entities->max('updatedAt'), $entities->max('createdAt')))
        ];

        $command = $this->commandFactory->create('ResponseDispatch');
        $command
            ->setParameter('request', $request)
            ->setParameter('response', $response)
            ->setParameter('body', $body);

        return $this->commandBus->handle($command);
    }

    /**
     * Retrieves one Feature of the User.
     *
     * @apiEndpointResponse 200 schema/feature/getOne.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @see \App\Repository\DBFeature::findByUserIdAndSlug
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getOne(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $user      = $request->getAttribute('targetUser');
        $handler   = $request->getAttribute('handler');
        $featureId = $request->getAttribute('decodedFeatureId');

        $feature = $this->repository->findOneByIdAndUserId($featureId, $user->id);

        if ($feature->source !== null) {
            $this->sourceRepository->findOneByName($feature->source, $user->id);
        }

        $body = [
            'data'    => $feature->toArray(),
            'updated' => $feature->updated_at
        ];

        $command = $this->commandFactory->create('ResponseDispatch');
        $command
            ->setParameter('request', $request)
            ->setParameter('response', $response)
            ->setParameter('body', $body);

        return $this->commandBus->handle($command);
    }

    /**
     * Creates a new Feture for the given user.
     *
     * @apiEndpointRequiredParam body string name  FriendCount Feature name
     * @apiEndpointRequiredParam body string value 17          Feature value
     * @apiEndpointRequiredParam body string type  integer     Feature type
     * @apiEndpointParam body int source_id 25367 Feature source_id
     * @apiEndpointResponse 201 schema/feature/createNew.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @see \App\Handler\Feature::handleCreateNew
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function createNew(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $user        = $request->getAttribute('targetUser');
        $handler     = $request->getAttribute('handler');
        $credential  = $request->getAttribute('credential');
        $source      = null;
        $sourceId    = $request->getParsedBodyParam('source_id');

        if ($sourceId !== null) {
            $source = $this->sourceRepository->findOne($request->getParsedBodyParam('decoded_source_id'), $user->id);
        }

        $command = $this->commandFactory->create('Profile\Feature\CreateNew');
        $command
            ->setParameters($request->getParsedBody() ?: [])
            ->setParameter('user', $user)
            ->setParameter('credential', $credential)
            ->setParameter('source', $source)
            ->setParameter('handler', $handler);

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
     * Updates one Feature of the User.
     *
     * @apiEndpointRequiredParam body string name FriendCount  Feature name
     * @apiEndpointRequiredParam body string value 20 Feature value
     * @apiEndpointRequiredParam body string type integer Feature type
     * @apiEndpointParam body int source_id 25367 Feature source_id
     * @apiEndpointResponse 200 schema/feature/updateOne.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @see \App\Handler\Feature::handleUpdateOne
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function updateOne(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $user         = $request->getAttribute('targetUser');
        $handler      = $request->getAttribute('handler');
        $credential   = $request->getAttribute('credential');
        $featureId    = $request->getAttribute('decodedFeatureId');

        $command = $this->commandFactory->create('Profile\Feature\UpdateOne');
        $command
            ->setParameters($request->getParsedBody() ?: [])
            ->setParameter('user', $user)
            ->setParameter('handler', $handler)
            ->setParameter('credential', $credential)
            ->setParameter('featureId', $featureId);

        $feature = $this->commandBus->handle($command);

        $body = [
            'data'    => $feature->toArray(),
            'updated' => $feature->updated_at
        ];

        $command = $this->commandFactory->create('ResponseDispatch');
        $command
            ->setParameter('request', $request)
            ->setParameter('response', $response)
            ->setParameter('body', $body);

        return $this->commandBus->handle($command);
    }

    /**
     * Creates or updates a feature for the given user.
     *
     * @apiEndpointRequiredParam body string name FriendCount  Feature name
     * @apiEndpointRequiredParam body string value 10 Feature value
     * @apiEndpointRequiredParam body string type integer Feature type
     * @apiEndpointParam body int source_id 25367 Feature source_id
     * @apiEndpointResponse 200 schema/feature/upsertOne.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function upsertOne(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $user        = $request->getAttribute('targetUser');
        $handler     = $request->getAttribute('handler');
        $credential  = $request->getAttribute('credential');
        $source      = null;
        $sourceId    = $request->getParsedBodyParam('source_id');

        if ($sourceId !== null) {
            $source = $this->sourceRepository->findOne($request->getParsedBodyParam('decoded_source_id'), $user->id);
        }

        $command = $this->commandFactory->create('Profile\Feature\UpsertOne');
        $command
            ->setParameters($request->getParsedBody() ?: [])
            ->setParameter('user', $user)
            ->setParameter('credential', $credential)
            ->setParameter('source', $source)
            ->setParameter('handler', $handler);

        $feature = $this->commandBus->handle($command);

        $body = [
            'status' => true,
            'data'   => $feature->toArray()
        ];

        $command = $this->commandFactory->create('ResponseDispatch');
        $command
            ->setParameter('request', $request)
            ->setParameter('response', $response)
            ->setParameter('body', $body);

        return $this->commandBus->handle($command);
    }

    /**
     * Creates or updates features for the given user.
     *
     * @apiEndpointRequiredParam body array features [] Feature features
     * @apiEndpointResponse 200 schema/feature/upsertBulk.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function upsertBulk(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $user        = $request->getAttribute('targetUser');
        $handler     = $request->getAttribute('handler');
        $credential  = $request->getAttribute('credential');

        $command = $this->commandFactory->create('Profile\Feature\UpsertBulk');
        $command
            ->setParameter('features', $request->getParsedBody())
            ->setParameter('user', $user)
            ->setParameter('credential', $credential)
            ->setParameter('handler', $handler);

        $features = $this->commandBus->handle($command);

        $command = $this->commandFactory->create('ResponseDispatch');
        $command
            ->setParameter('request', $request)
            ->setParameter('response', $response)
            ->setParameter('body', ['data' => $features->toArray()]);

        return $this->commandBus->handle($command);
    }

    /**
     * Deletes one Feature of the User.
     *
     * @apiEndpointResponse 200 schema/feature/deleteOne.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @see \App\Handler\Feature::handleDeleteOne
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function deleteOne(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $user         = $request->getAttribute('targetUser');
        $handler      = $request->getAttribute('handler');
        $credential   = $request->getAttribute('credential');
        $featureId    = $request->getAttribute('decodedFeatureId');

        $command = $this->commandFactory->create('Profile\Feature\DeleteOne');
        $command
            ->setParameter('user', $user)
            ->setParameter('handler', $handler)
            ->setParameter('credential', $credential)
            ->setParameter('featureId', $featureId);

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
     * Deletes all Features that belongs to the User.
     *
     * @apiEndpointResponse 200 schema/feature/deleteAll.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @see \App\Handler\Feature::handleDeleteAll
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function deleteAll(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $user       = $request->getAttribute('targetUser');
        $handler    = $request->getAttribute('handler');
        $credential = $request->getAttribute('credential');

        $command = $this->commandFactory->create('Profile\Feature\DeleteAll');
        $command
            ->setParameter('credential', $credential)
            ->setParameter('user', $user)
            ->setParameter('handler', $handler)
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
