<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Controller;

use App\Entity\User;
use App\Factory\Command;
use App\Repository\RawInterface;
use App\Repository\SourceInterface;
use League\Tactician\CommandBus;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Handles requests to /profiles/{userName}/sources/{sourceId:[0-9]+}/raw.
 */
class Raw implements ControllerInterface {
    /**
     * Raw Repository instance.
     *
     * @var App\Repository\RawInterface
     */
    private $repository;
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
     * @param App\Repository\RawInterface    $repository
     * @param App\Repository\SourceInterface $sourceRepository
     * @param \League\Tactician\CommandBus   $commandBus
     * @param App\Factory\Command            $commandFactory
     *
     * @return void
     */
    public function __construct(
        RawInterface $repository,
        SourceInterface $sourceRepository,
        CommandBus $commandBus,
        Command $commandFactory
    ) {
        $this->repository       = $repository;
        $this->sourceRepository = $sourceRepository;
        $this->commandBus       = $commandBus;
        $this->commandFactory   = $commandFactory;
    }

    /**
     * Retrieve a complete list of the raw data by a given source.
     *
     * @apiEndpointURIFragment string userName usr001
     * @apiEndpointURIFragment int    sourceId 1
     * @apiEndpointParam       query  string   names  numOfFriends
     * @apiEndpointResponse 200 schema/raw/listAll.json
     *
     * @param \Psr\ServerRequestInterface $request
     * @param \Psr\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function listAll(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $user   = $request->getAttribute('targetUser');
        $source = $this->sourceRepository->findOne((int) $request->getAttribute('decodedSourceId'), $user->id);
        $names  = $request->getQueryParam('names', []);

        if ($names) {
            $names = explode(',', $names);
        }

        $raws = $this->repository->getAllBySourceAndNames($source, $names);

        $body = [
            'data'    => $raws->toArray(),
            'updated' => (
                $raws->isEmpty() ? time() : max($raws->max('updatedAt'), $raws->max('createdAt'))
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
     * Created a new raw data for a given source.
     *
     * @apiEndpointResponse 201 schema/raw/rawEntity.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function createNew(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $command = $this->commandFactory->create('Raw\\CreateNew');

        $user   = $request->getAttribute('targetUser');
        $source = $this->sourceRepository->findOne((int) $request->getAttribute('decodedSourceId'), $user->id);

        $command
            ->setParameters($request->getParsedBody())
            ->setParameter('user', $user)
            ->setParameter('source', $source);

        $raw = $this->commandBus->handle($command);

        $body = [
            'status' => true,
            'data'   => $raw->toArray()
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
     * Updates a raw data from the given source.
     *
     * @apiEndpointURIFragment   string rawName numOfFriends
     * @apiEndpointRequiredParam body   string       data        1
     * @apiEndpointResponse 200 schema/raw/updateOne.json
     *
     * @param \Psr\ServerRequestInterface $request
     * @param \Psr\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function updateOne(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $command = $this->commandFactory->create('Raw\\UpdateOne');

        $user   = $request->getAttribute('targetUser');
        $source = $this->sourceRepository->findOne((int) $request->getAttribute('decodedSourceId'), $user->id);

        $command
            ->setParameters($request->getParsedBody())
            ->setParameter('user', $user)
            ->setParameter('source', $source)
            ->setParameter('name', $request->getAttribute('rawName'));

        $raw = $this->commandBus->handle($command);

        $body = [
            'data'    => $raw->toArray(),
            'updated' => $raw->updated_at
        ];

        $command = $this->commandFactory->create('ResponseDispatch');
        $command
            ->setParameter('request', $request)
            ->setParameter('response', $response)
            ->setParameter('body', $body);

        return $this->commandBus->handle($command);
    }

    /**
     * Retrieves a raw data from the given source.
     *
     * @apiEndpointURIFragment string userName     usr001
     * @apiEndpointURIFragment int    sourceId     1
     * @apiEndpointURIFragment string rawName numOfFriends
     * @apiEndpointResponse 200 schema/raw/rawEntity.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getOne(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $user   = $request->getAttribute('targetUser');
        $source = $this->sourceRepository->findOne((int) $request->getAttribute('decodedSourceId'), $user->id);

        $raw = $this->repository->findOneBySourceAndName($source, $request->getAttribute('rawName'));

        $body = [
            'data' => $raw->toArray()
        ];

        $command = $this->commandFactory->create('ResponseDispatch');
        $command
            ->setParameter('request', $request)
            ->setParameter('response', $response)
            ->setParameter('body', $body);

        return $this->commandBus->handle($command);
    }

    /**
     * Deletes all raw data from a given source.
     *
     * @apiEndpointResponse 200 schema/raw/deleteAll.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function deleteAll(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $command = $this->commandFactory->create('Raw\\DeleteAll');

        $user   = $request->getAttribute('targetUser');
        $source = $this->sourceRepository->findOne((int) $request->getAttribute('decodedSourceId'), $user->id);

        $command
            ->setParameter('user', $user)
            ->setParameter('source', $source);

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
     * Deletes a raw data from a given source.
     *
     * @apiEndpointURIFragment string userName     usr001
     * @apiEndpointURIFragment int    sourceId     1
     * @apiEndpointURIFragment string rawName numOfFriends
     * @apiEndpointResponse    200    schema/raw/deleteOne.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function deleteOne(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $command = $this->commandFactory->create('Raw\\DeleteOne');

        $user   = $request->getAttribute('targetUser');
        $source = $this->sourceRepository->findOne((int) $request->getAttribute('decodedSourceId'), $user->id);

        $command
            ->setParameter('user', $user)
            ->setParameter('source', $source)
            ->setParameter('name', $request->getAttribute('rawName'));

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
