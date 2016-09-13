<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Controller;

use App\Exception\NotFound;
use App\Factory\Command;
use App\Repository\FeatureInterface;
use App\Repository\UserInterface;
use App\Repository\SourceInterface;
use League\Tactician\CommandBus;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Illuminate\Support\Collection;

/**
 * Handles requests to /profiles/:userName/features.
 */
class Features implements ControllerInterface {
    /**
     * Setting Repository instance.
     *
     * @var App\Repository\FeatureInterface
     */
    private $repository;
    /**
     * User Repository instance.
     *
     * @var App\Repository\UserInterface
     */
    private $userRepository;
    /**
     * Source Repository instance.
     *
     * @var App\Repository\SourceInterface
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
     * @var App\Factory\Command
     */
    private $commandFactory;

    /**
     * Class constructor.
     *
     * @param App\Repository\FeatureInterface $repository
     * @param App\Repository\UserInterface    $userRepository
     * @param \League\Tactician\CommandBus    $commandBus
     * @param App\Factory\Command             $commandFactory
     *
     * @return void
     */
    public function __construct(
        FeatureInterface $repository,
        UserInterface $userRepository,
        SourceInterface $sourceRepository,
        CommandBus $commandBus,
        Command $commandFactory
    ) {
        $this->repository     = $repository;
        $this->userRepository = $userRepository;
        $this->sourceRepository = $sourceRepository;
        $this->commandBus     = $commandBus;
        $this->commandFactory = $commandFactory;
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
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function listAll(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $user   = $request->getAttribute('targetUser');
        $service = $request->getAttribute('service');
        $entities = $this->repository->findBy([
            'user_id' => $user->id,
            'creator' => $service->id
        ], $request->getQueryParams());

        $body = [
            'data'       => $entities->toArray(),
            'updated'    => (
                $entities->isEmpty() ? null : max($entities->max('updatedAt'), $entities->max('createdAt'))
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
     * Retrieves one Feature of the User.
     *
     * @apiEndpointResponse 200 schema/feature/getOne.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getOne(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $user        = $request->getAttribute('targetUser');
        $service = $request->getAttribute('service');
        $featureId = $request->getAttribute('decodedFeatureId');

        $feature = $this->repository->findOneBy([
            'user_id' => $user->id,
            'creator' => $service->id,
            'id' => $featureId
        ]);

        if ($feature->sourceId !== null && $feature->sourceId !== $user->id) {
            throw new NotFound();
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
     * @apiEndpointRequiredParam body string name XYZ Feature name
     * @apiEndpointRequiredParam body string value ZYX Feature value
     * @apiEndpointResponse 201 schema/feature/createNew.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function createNew(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $user = $request->getAttribute('targetUser');
        $source = $request->getParsedBodyParam('sourceId') !== 0 ? $this->sourceRepository->find($request->getParsedBodyParam('sourceId'), $user->id) : null;
        $service = $request->getAttribute('service');

        $command = $this->commandFactory->create('Feature\\CreateNew');
        $command
            ->setParameters($request->getParsedBody())
            ->setParameter('user', $user)
            ->setParameter('source', $source)
            ->setParameter('service', $service);

        $feature = $this->commandBus->handle($command);

        $body = [
            'status' => true,
            'data'   => $feature->toArray()
        ];

        $command = $this->commandFactory->create('ResponseDispatch');
        $command
            ->setParameter('statusCode', isset($feature->updatedAt) ? 200 : 201)
            ->setParameter('request', $request)
            ->setParameter('response', $response)
            ->setParameter('body', $body);

        return $this->commandBus->handle($command);
    }

    /**
     * Deletes all Features that belongs to the User.
     *
     * @apiEndpointResponse 200 schema/setting/deleteAll.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function deleteAll(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $user = $request->getAttribute('targetUser');
        $service = $request->getAttribute('service');

        $command = $this->commandFactory->create('Feature\\DeleteAll');
        $command->setParameter('user', $user)
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

    /**
     * Deletes one Feature of the User.
     *
     * @apiEndpointResponse 200 schema/feature/deleteOne.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function deleteOne(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $user        = $request->getAttribute('targetUser');
        $service = $request->getAttribute('service');
        $featureId = $request->getAttribute('decodedFeatureId');

        $command = $this->commandFactory->create('Feature\\DeleteOne');
        $command->setParameter('user', $user)
            ->setParameter('service', $service)
            ->setParameter('featureId', $featureId);

        $body = [
            'status' => (bool) $this->commandBus->handle($command)
        ];

        $command = $this->commandFactory->create('ResponseDispatch');
        $command
            ->setParameter('request', $request)
            ->setParameter('response', $response)
            ->setParameter('body', $body);

        return $this->commandBus->handle($command);
    }

    /**
     * Updates one Feature of the User.
     *
     * @apiEndpointRequiredParam body string name XYZ Feature name
     * @apiEndpointRequiredParam body string value ZYX Feature value
     * @apiEndpointResponse 200 schema/feature/updateOne.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function updateOne(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $user        = $request->getAttribute('targetUser');
        $source = $this->sourceRepository->find($request->getParsedBodyParam('sourceId'), $user->id);
        $service = $request->getAttribute('service');
        $featureId = $request->getAttribute('decodedFeatureId');

        $command = $this->commandFactory->create('Feature\\UpdateOne');
        $command
            ->setParameters($request->getParsedBody())
            ->setParameter('user', $user)
            ->setParameter('source', $source)
            ->setParameter('service', $service)
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
}
