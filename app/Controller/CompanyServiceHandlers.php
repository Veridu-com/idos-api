<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types=1);

namespace App\Controller;

use App\Factory\Command;
use App\Repository\CompanyServiceHandlerInterface;
use League\Tactician\CommandBus;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Handles requests to /company-service-handlers.
 */
class CompanyServiceHandlers implements ControllerInterface {
    /**
     * CompanyServiceHandler Repository instance.
     *
     * @var App\Repository\CompanyServiceHandlerInterface
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
     * @param App\Repository\CompanyServiceHandlerInterface $repository
     * @param \League\Tactician\CommandBus                  $commandBus
     * @param App\Factory\Command                           $commandFactory
     *
     * @return void
     */
    public function __construct(
        CompanyServiceHandlerInterface $repository,
        CommandBus $commandBus,
        Command $commandFactory
    ) {
        $this->repository     = $repository;
        $this->commandBus     = $commandBus;
        $this->commandFactory = $commandFactory;
    }

    /**
     * Lists all Service handlers that belongs to the acting Company.
     *
     * @apiEndpointParam    query   int     page 10|1 Current page
     * 
     * @apiEndpointResponse 200 schema/company-service-handlers/listAll.json
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
     * Retrieves one CompanyServiceHandler of the acting Company and has the given id.
     * 
     * @apiEndpointRequiredParam    route   int     id
     * @apiEndpointParam            query   int     page 10|1 Current page
     * 
     * @apiEndpointResponse 200 schema/company-services-handlers/getOne.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getOne(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $actingCompany = $request->getAttribute('actingCompany');
        $id            = (int) $request->getAttribute('id');

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
     * Creates a new CompanyServiceHandler for the acting Company.
     *
     * @apiEndpointRequiredParam    body    int     service_handler_id     Service handler's id.    
     * 
     * @apiEndpointResponse 201 schema/company-services-handlers/createNew.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function createNew(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $actingCompany = $request->getAttribute('actingCompany');

        $command = $this->commandFactory->create('CompanyServiceHandler\\CreateNew');
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
     * Deletes all Service handlers that belongs to the acting Company.
     *
     * @apiEndpointResponse 200 schema/company-services-handlers/deleteAll.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function deleteAll(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $actingCompany = $request->getAttribute('actingCompany');

        $command = $this->commandFactory->create('CompanyServiceHandler\\DeleteAll');
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
     * Deletes one Company Service handler of the acting Company.
     *
     * @apiEndpointRequiredParam    route   int     id
     * 
     * @apiEndpointResponse 200 schema/company-services-handlers/deleteOne.json
     * 
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function deleteOne(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $actingCompany  = $request->getAttribute('actingCompany');
        $id             = (int) $request->getAttribute('id');

        $command = $this->commandFactory->create('CompanyServiceHandler\\DeleteOne');
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
