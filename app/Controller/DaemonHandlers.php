<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types=1);

namespace App\Controller;

use App\Factory\Command;
use App\Repository\DaemonHandlerInterface;
use League\Tactician\CommandBus;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Handles requests to /daemon-handlers.
 */
class DaemonHandlers implements ControllerInterface {
    /**
     * DaemonHandler Repository instance.
     *
     * @var App\Repository\DaemonHandlerInterface
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
     * @param App\Repository\DaemonHandlerInterface $repository
     * @param \League\Tactician\CommandBus           $commandBus
     * @param App\Factory\Command                    $commandFactory
     *
     * @return void
     */
    public function __construct(
        DaemonHandlerInterface $repository,
        CommandBus $commandBus,
        Command $commandFactory
    ) {
        $this->repository     = $repository;
        $this->commandBus     = $commandBus;
        $this->commandFactory = $commandFactory;
    }

    /**
     * Lists all Daemon handlers that belongs to the acting Company.
     *
     * @apiEndpointParam    query   int     page 10|1 Current page
     * 
     * @apiEndpointResponse 200 schema/daemon-handlers/listAll.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function listAll(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $actingCompany = $request->getAttribute('actingCompany');
        $entities      = $this->repository->getAllByCompanyId($actingCompany->id);

        $body = [
            'data'    => $entities->toArray(),
            'updated' => (
                $entities->isEmpty() ? time() : $entities->max('updated_at')
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
     * Retrieves one DaemonHandler of the acting Company and daemon and has the given daemonHandlerSlug.
     * 
     * @apiEndpointRequiredParam    route   string  daemon slug 
     * @apiEndpointRequiredParam    route   string  daemon handler slug
     * 
     * @apiEndpointParam            query   int     page 10|1 Current page
     * 
     * @apiEndpointResponse 200 schema/daemons-handlers/getOne.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getOne(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $id            = $request->getAttribute('decodedDaemonHandlerId');
        $entity        = $this->repository->find($id);

        $body = [
            'data'    => $entity->toArray()
        ];

        $command = $this->commandFactory->create('ResponseDispatch');
        $command
            ->setParameter('request', $request)
            ->setParameter('response', $response)
            ->setParameter('body', $body);

        return $this->commandBus->handle($command);
    }

    /**
     * Creates a new DaemonHandler for the acting Company.
     *
     * @apiEndpointRequiredParam    body    string      name            Daemon handler's name.    
     * @apiEndpointRequiredParam    body    string      source          Daemon handler's source.    
     * @apiEndpointRequiredParam    body    string      location        Daemon handler's location.    
     * @apiEndpointRequiredParam    body    string      authUsername    Daemon handler's authUsername.    
     * @apiEndpointRequiredParam    body    string      authPassword    Daemon handler's authPassword.    
     * @apiEndpointRequiredParam    body    string      daemon         Daemon's slug.
     * 
     * @apiEndpointResponse 201 schema/daemons-handlers/createNew.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function createNew(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $actingCompany = $request->getAttribute('actingCompany');

        $command = $this->commandFactory->create('DaemonHandler\\CreateNew');
        $command
            ->setParameters($request->getParsedBody())
            ->setParameter('companyId', $actingCompany->id);

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
     * Deletes all Daemon handlers that belongs to the acting Company.
     *
     * @apiEndpointResponse 200 schema/daemons-handlers/deleteAll.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function deleteAll(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $actingCompany = $request->getAttribute('actingCompany');

        $command = $this->commandFactory->create('DaemonHandler\\DeleteAll');
        $command->setParameter('companyId', $actingCompany->id);

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
     * Deletes one Daemon handler of the acting Company based on path paramaters daemon slug and slug.
     *
     * @apiEndpointResponse 200 schema/daemons-handlers/deleteOne.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function deleteOne(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $id            = $request->getAttribute('decodedDaemonHandlerId');

        $command = $this->commandFactory->create('DaemonHandler\\DeleteOne');
        $command
            ->setParameter('daemonHandlerId', $id);

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
     * Updates one Daemon handler of the acting Company based on path paramaters daemon and slug.
     *
     * @apiEndpointRequiredParam    route   string      decodedDaemonHandlerId  Daemon handler's slug.
     * @apiEndpointParam            route   string      daemon                  Daemon's slug.
     * @apiEndpointParam            body    string      name                    Daemon handler's name.    
     * @apiEndpointParam            body    string      runLevel                Daemon handler's source.    
     * @apiEndpointParam            body    string      step                    Daemon handler's source.    
     * @apiEndpointParam            body    string      source                  Daemon handler's source.    
     * @apiEndpointParam            body    string      location                Daemon handler's location.    
     * @apiEndpointParam            body    string      authUsername            Daemon handler's authUsername.    
     * @apiEndpointParam            body    string      authPassword            Daemon handler's authPassword.    
     * 
     * @apiEndpointResponse 200 schema/daemons-handlers/updateOne.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function updateOne(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $daemonHandlerId = $request->getAttribute('decodedDaemonHandlerId');

        $command = $this->commandFactory->create('DaemonHandler\\UpdateOne');
        $command
            ->setParameters($request->getParsedBody())
            ->setParameter('daemonHandlerId', $daemonHandlerId);

        $entity = $this->commandBus->handle($command);

        $body = [
            'data'    => $entity->toArray(),
            'updated' => $entity->updated_at
        ];

        $command = $this->commandFactory->create('ResponseDispatch');
        $command
            ->setParameter('request', $request)
            ->setParameter('response', $response)
            ->setParameter('body', $body);

        return $this->commandBus->handle($command);
    }
}
