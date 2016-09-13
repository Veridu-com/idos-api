<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Controller;

use App\Entity\User;
use App\Factory\Command;
use App\Repository\NormalisedInterface;
use League\Tactician\CommandBus;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Handles requests to /profiles/{userName}/sources/{sourceId:[0-9]+}/normalised.
 */
class Normalised implements ControllerInterface {
    /**
     * Normalised Repository instance.
     *
     * @var App\Repository\NormalisedInterface
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
     * @param App\Repository\NormalisedInterface $repository
     * @param \League\Tactician\CommandBus       $commandBus
     * @param App\Factory\Command                $commandFactory
     *
     * @return void
     */
    public function __construct(
        NormalisedInterface $repository,
        CommandBus $commandBus,
        Command $commandFactory
    ) {
        $this->repository     = $repository;
        $this->commandBus     = $commandBus;
        $this->commandFactory = $commandFactory;
    }

    /**
     * Retrieve a list of the data normalised entries of a source.
     *
     * @apiEndpointURIFragment string userName usr001
     * @apiEndpointURIFragment int    sourceId 1
     * @apiEndpointParam       query  string   names firstName
     * @apiEndpointResponse    200    schema/normalised/listAll.json
     *
     * @param \Psr\ServerRequestInterface $request
     * @param \Psr\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function listAll(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $user     = $request->getAttribute('targetUser');
        $sourceId = (int) $request->getAttribute('decodedSourceId');

        $data = $this->repository->getAllByUserIdSourceIdAndNames($user->id, $sourceId, $request->getQueryParams());

        $body = [
            'data'    => $data->toArray(),
            'updated' => (
                $data->isEmpty() ? time() : max($data->max('updatedAt'), $data->max('createdAt'))
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
     * Created a new normalised data entry for a source.
     *
     * @apiEndpointRequiredParam body   string     name  firstName
     * @apiEndpointRequiredParam body   string     value John
     * @apiEndpointResponse 201 schema/normalised/normalisedEntity.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function createNew(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $command = $this->commandFactory->create('Normalised\\CreateNew');

        $command
            ->setParameters($request->getParsedBody())
            ->setParameter('user', $request->getAttribute('targetUser'))
            ->setParameter('sourceId', (int) $request->getAttribute('decodedSourceId'));

        $normalised = $this->commandBus->handle($command);

        $body = [
            'status' => true,
            'data'   => $normalised->toArray()
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
     * Updates a normalised data entry of a source.
     *
     * @apiEndpointURIFragment   string normalisedName firstName
     * @apiEndpointRequiredParam body   string     value     John
     * @apiEndpointResponse      200    schema/normalised/updateOne.json
     *
     * @param \Psr\ServerRequestInterface $request
     * @param \Psr\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function updateOne(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $command = $this->commandFactory->create('Normalised\\UpdateOne');
        $command
            ->setParameters($request->getParsedBody())
            ->setParameter('user', $request->getAttribute('targetUser'))
            ->setParameter('sourceId', (int) $request->getAttribute('decodedSourceId'))
            ->setParameter('name', $request->getAttribute('normalisedName'));

        $normalised = $this->commandBus->handle($command);

        $body = [
            'data'    => $normalised->toArray(),
            'updated' => $normalised->updated_at
        ];

        $command = $this->commandFactory->create('ResponseDispatch');
        $command
            ->setParameter('request', $request)
            ->setParameter('response', $response)
            ->setParameter('body', $body);

        return $this->commandBus->handle($command);
    }

    /**
     * Retrieves normalised data entries from a source.
     *
     * @apiEndpointURIFragment string userName usr001
     * @apiEndpointURIFragment int    sourceId 1
     * @apiEndpointParam       query  string   normalisedName firstName
     * @apiEndpointResponse 200 schema/normalised/normalisedEntity.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getOne(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $user     = $request->getAttribute('targetUser');
        $sourceId = (int) $request->getAttribute('decodedSourceId');
        $name     = $request->getAttribute('normalisedName');

        $normalised = $this->repository->findOneByUserIdSourceIdAndName($user->id, $sourceId, $name);

        $body = [
            'data' => $normalised->toArray()
        ];

        $command = $this->commandFactory->create('ResponseDispatch');
        $command
            ->setParameter('request', $request)
            ->setParameter('response', $response)
            ->setParameter('body', $body);

        return $this->commandBus->handle($command);
    }

    /**
     * Deletes all normalised data entries from a source.
     *
     * @apiEndpointResponse 200 schema/normalised/deleteAll.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function deleteAll(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $command = $this->commandFactory->create('Normalised\\DeleteAll');
        $command
            ->setParameter('user', $request->getAttribute('targetUser'))
            ->setParameter('sourceId', (int) $request->getAttribute('decodedSourceId'));

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
     * Deletes a normalised data entry from a source.
     *
     * @apiEndpointURIFragment string userName usr001
     * @apiEndpointURIFragment int    sourceId 1
     * @apiEndpointURIFragment string normalisedName firstName
     * @apiEndpointResponse 200 schema/normalised/deleteOne.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function deleteOne(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $command = $this->commandFactory->create('Normalised\\DeleteOne');
        $command
            ->setParameter('user', $request->getAttribute('targetUser'))
            ->setParameter('sourceId', (int) $request->getAttribute('decodedSourceId'))
            ->setParameter('name', $request->getAttribute('normalisedName'));

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
