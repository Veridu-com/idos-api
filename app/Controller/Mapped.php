<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Controller;

use App\Entity\User;
use App\Factory\Command;
use App\Repository\MappedInterface;
use League\Tactician\CommandBus;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Handles requests to /profiles/{userName}/sources/{sourceId}/mapped.
 */
class Mapped implements ControllerInterface {
    /**
     * Mapped Repository instance.
     *
     * @var App\Repository\MappedInterface
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
     * @param App\Repository\MappedInterface $repository
     * @param \League\Tactician\CommandBus   $commandBus
     * @param App\Factory\Command            $commandFactory
     *
     * @return void
     */
    public function __construct(
        MappedInterface $repository,
        CommandBus $commandBus,
        Command $commandFactory
    ) {
        $this->repository     = $repository;
        $this->commandBus     = $commandBus;
        $this->commandFactory = $commandFactory;
    }

    /**
     * Retrieve a complete list of the data mapped by a given source.
     *
     * @apiEndpointParam path string userName
     * @apiEndpointParam path int sourceId
     * @apiEndpointParam query string names
     * @apiEndpointResponse 200 schema/mapped/listAll.json
     *
     * @param \Psr\ServerRequestInterface $request
     * @param \Psr\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function listAll(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $user     = $request->getAttribute('targetUser');
        $sourceId = (int) $request->getAttribute('sourceId');
        $names    = $request->getQueryParam('names', []);

        if ($names) {
            $names = explode(',', $names);
        }

        $mappeds = $this->repository->getAllByUserIdSourceIdAndNames($user->id, $sourceId, $names);

        $body = [
            'data'    => $mappeds->toArray(),
            'updated' => (
                $mappeds->isEmpty() ? time() : max($mappeds->max('updatedAt'), $mappeds->max('createdAt'))
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
     * Created a new mapped data for a given source.
     *
     * @apiEndpointResponse 201 schema/mapped/mappedEntity.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function createNew(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $command = $this->commandFactory->create('Mapped\\CreateNew');

        $command
            ->setParameters($request->getParsedBody())
            ->setParameter('user', $request->getAttribute('targetUser'))
            ->setParameter('sourceId', (int) $request->getAttribute('sourceId'));

        $mapped = $this->commandBus->handle($command);

        $body = [
            'status' => true,
            'data'   => $mapped->toArray()
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
     * Updates a mapped data from the given source.
     *
     * @apiEndpointRequiredParam body string value
     * @apiEndpointResponse 200 schema/mapped/updateOne.json
     *
     * @param \Psr\ServerRequestInterface $request
     * @param \Psr\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function updateOne(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $command = $this->commandFactory->create('Mapped\\UpdateOne');
        $command
            ->setParameters($request->getParsedBody())
            ->setParameter('user', $request->getAttribute('targetUser'))
            ->setParameter('sourceId', (int) $request->getAttribute('sourceId'))
            ->setParameter('name', $request->getAttribute('mappedName'));

        $mapped = $this->commandBus->handle($command);

        $body = [
            'data'    => $mapped->toArray(),
            'updated' => $mapped->updated_at
        ];

        $command = $this->commandFactory->create('ResponseDispatch');
        $command
            ->setParameter('request', $request)
            ->setParameter('response', $response)
            ->setParameter('body', $body);

        return $this->commandBus->handle($command);
    }

    /**
     * Retrieves a mapped data from the given source.
     *
     * @apiEndpointParam path string userName
     * @apiEndpointParam path int sourceId
     * @apiEndpointParam query string mappedName
     * @apiEndpointResponse 200 schema/mapped/mappedEntity.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getOne(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $user     = $request->getAttribute('targetUser');
        $sourceId = (int) $request->getAttribute('sourceId');
        $name     = $request->getAttribute('mappedName');

        $mapped = $this->repository->findOneByUserIdSourceIdAndName($user->id, $sourceId, $name);

        $body = [
            'data' => $mapped->toArray()
        ];

        $command = $this->commandFactory->create('ResponseDispatch');
        $command
            ->setParameter('request', $request)
            ->setParameter('response', $response)
            ->setParameter('body', $body);

        return $this->commandBus->handle($command);
    }

    /**
     * Deletes all mapped data from a given source.
     *
     * @apiEndpointResponse 200 schema/mapped/deleteAll.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function deleteAll(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $command = $this->commandFactory->create('Mapped\\DeleteAll');
        $command
            ->setParameter('user', $request->getAttribute('targetUser'))
            ->setParameter('sourceId', (int) $request->getAttribute('sourceId'));

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
     * Deletes a mapped data from a given source.
     *
     * @apiEndpointParam path string userName
     * @apiEndpointParam path int sourceId
     * @apiEndpointParam query string mappedName
     * @apiEndpointResponse 200 schema/mapped/deleteOne.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function deleteOne(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $command = $this->commandFactory->create('Mapped\\DeleteOne');
        $command
            ->setParameter('user', $request->getAttribute('targetUser'))
            ->setParameter('sourceId', (int) $request->getAttribute('sourceId'))
            ->setParameter('name', $request->getAttribute('mappedName'));

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
