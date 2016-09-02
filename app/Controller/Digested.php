<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Controller;

use App\Entity\User;
use App\Factory\Command;
use App\Repository\DigestedInterface;
use League\Tactician\CommandBus;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Handles requests to /profiles/{userName}/sources/{sourceId:[0-9]+}/digested.
 */
class Digested implements ControllerInterface {
    /**
     * Digested Repository instance.
     *
     * @var App\Repository\DigestedInterface
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
     * @param App\Repository\DigestedInterface $repository
     * @param \League\Tactician\CommandBus     $commandBus
     * @param App\Factory\Command              $commandFactory
     *
     * @return void
     */
    public function __construct(
        DigestedInterface $repository,
        CommandBus $commandBus,
        Command $commandFactory
    ) {
        $this->repository     = $repository;
        $this->commandBus     = $commandBus;
        $this->commandFactory = $commandFactory;
    }

    /**
     * Retrieve a complete list of the data digested by a given source.
     *
     * @apiEndpointURIFragment string userName usr001
     * @apiEndpointURIFragment int    sourceId 1
     * @apiEndpointParam       query  string   names  numOfFriends
     * @apiEndpointResponse 200 schema/digested/listAll.json
     *
     * @param \Psr\ServerRequestInterface $request
     * @param \Psr\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function listAll(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $user     = $request->getAttribute('targetUser');
        $sourceId = (int) $request->getAttribute('decodedSourceId');
        $names    = $request->getQueryParam('names', []);

        if ($names) {
            $names = explode(',', $names);
        }

        $digesteds = $this->repository->getAllByUserIdSourceIdAndNames($user->id, $sourceId, $names);

        $body = [
            'data'    => $digesteds->toArray(),
            'updated' => (
                $digesteds->isEmpty() ? time() : max($digesteds->max('updatedAt'), $digesteds->max('createdAt'))
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
     * Created a new digested data for a given source.
     *
     * @apiEndpointResponse 201 schema/digested/digestedEntity.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function createNew(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $command = $this->commandFactory->create('Digested\\CreateNew');

        $command
            ->setParameters($request->getParsedBody())
            ->setParameter('user', $request->getAttribute('targetUser'))
            ->setParameter('sourceId', (int) $request->getAttribute('decodedSourceId'));

        $digested = $this->commandBus->handle($command);

        $body = [
            'status' => true,
            'data'   => $digested->toArray()
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
     * Updates a digested data from the given source.
     *
     * @apiEndpointURIFragment   string digestedName numOfFriends
     * @apiEndpointRequiredParam body   string       value        1
     * @apiEndpointResponse 200 schema/digested/updateOne.json
     *
     * @param \Psr\ServerRequestInterface $request
     * @param \Psr\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function updateOne(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $command = $this->commandFactory->create('Digested\\UpdateOne');
        $command
            ->setParameters($request->getParsedBody())
            ->setParameter('user', $request->getAttribute('targetUser'))
            ->setParameter('sourceId', (int) $request->getAttribute('decodedSourceId'))
            ->setParameter('name', $request->getAttribute('digestedName'));

        $digested = $this->commandBus->handle($command);

        $body = [
            'data'    => $digested->toArray(),
            'updated' => $digested->updated_at
        ];

        $command = $this->commandFactory->create('ResponseDispatch');
        $command
            ->setParameter('request', $request)
            ->setParameter('response', $response)
            ->setParameter('body', $body);

        return $this->commandBus->handle($command);
    }

    /**
     * Retrieves a digested data from the given source.
     *
     * @apiEndpointURIFragment string userName     usr001
     * @apiEndpointURIFragment int    sourceId     1
     * @apiEndpointURIFragment string digestedName numOfFriends
     * @apiEndpointResponse 200 schema/digested/digestedEntity.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getOne(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $user     = $request->getAttribute('targetUser');
        $sourceId = (int) $request->getAttribute('decodedSourceId');
        $name     = $request->getAttribute('digestedName');

        $digested = $this->repository->findOneByUserIdSourceIdAndName($user->id, $sourceId, $name);

        $body = [
            'data' => $digested->toArray()
        ];

        $command = $this->commandFactory->create('ResponseDispatch');
        $command
            ->setParameter('request', $request)
            ->setParameter('response', $response)
            ->setParameter('body', $body);

        return $this->commandBus->handle($command);
    }

    /**
     * Deletes all digested data from a given source.
     *
     * @apiEndpointResponse 200 schema/digested/deleteAll.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function deleteAll(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $command = $this->commandFactory->create('Digested\\DeleteAll');
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
     * Deletes a digested data from a given source.
     *
     * @apiEndpointURIFragment string userName     usr001
     * @apiEndpointURIFragment int    sourceId     1
     * @apiEndpointURIFragment string digestedName numOfFriends
     * @apiEndpointResponse    200    schema/digested/deleteOne.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function deleteOne(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $command = $this->commandFactory->create('Digested\\DeleteOne');
        $command
            ->setParameter('user', $request->getAttribute('targetUser'))
            ->setParameter('sourceId', (int) $request->getAttribute('decodedSourceId'))
            ->setParameter('name', $request->getAttribute('digestedName'));

        $deleted = $this->commandBus->handle($command);

        $body = [
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
