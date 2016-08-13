<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types=1);

namespace App\Controller;

use App\Factory\Command;
use App\Repository\CompanyDaemonHandlerInterface;
use League\Tactician\CommandBus;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Handles requests to /company-daemon-handlers.
 */
class CompanyDaemonHandlers implements ControllerInterface {
    /**
     * CompanyDaemonHandler Repository instance.
     *
     * @var App\Repository\CompanyDaemonHandlerInterface
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
     * @param App\Repository\CompanyDaemonHandlerInterface $repository
     * @param \League\Tactician\CommandBus                  $commandBus
     * @param App\Factory\Command                           $commandFactory
     *
     * @return void
     */
    public function __construct(
        CompanyDaemonHandlerInterface $repository,
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
     * @apiEndpointResponse 200 schema/company-daemon-handlers/listAll.json
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
     * Retrieves one CompanyDaemonHandler of the acting Company and has the given id.
     * 
     * @apiEndpointRequiredParam    route   int     id
     * @apiEndpointParam            query   int     page 10|1 Current page
     * 
     * @apiEndpointResponse 200 schema/company-daemons-handlers/getOne.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getOne(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $actingCompany = $request->getAttribute('actingCompany');
        $id            = (int) $request->getAttribute('decodedCompanyDaemonHandlerId');

        $entity = $this->repository->findOne($id, $actingCompany->id);

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
     * Creates a new CompanyDaemonHandler for the acting Company.
     *
     * @apiEndpointRequiredParam    body    int     daemon_handler_id     Daemon handler's id.    
     * 
     * @apiEndpointResponse 201 schema/company-daemons-handlers/createNew.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function createNew(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $actingCompany = $request->getAttribute('actingCompany');

        $command = $this->commandFactory->create('CompanyDaemonHandler\\CreateNew');
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
     * @apiEndpointResponse 200 schema/company-daemons-handlers/deleteAll.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function deleteAll(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $actingCompany = $request->getAttribute('actingCompany');

        $command = $this->commandFactory->create('CompanyDaemonHandler\\DeleteAll');
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
     * Deletes one Company Daemon handler of the acting Company.
     *
     * @apiEndpointRequiredParam    route   int     id
     * 
     * @apiEndpointResponse 200 schema/company-daemons-handlers/deleteOne.json
     * 
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function deleteOne(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $actingCompany  = $request->getAttribute('actingCompany');
        $id             = (int) $request->getAttribute('id');

        $command = $this->commandFactory->create('CompanyDaemonHandler\\DeleteOne');
        $command
            ->setParameter('id', $id)
            ->setParameter('companyId', $actingCompany->id);

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
}
