<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Controller;

use App\Factory\Command;
use App\Repository\ServiceHandlerInterface;
use League\Tactician\CommandBus;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Handles requests to /service-handlers.
 */
class ServiceHandlers implements ControllerInterface {
    /**
     * ServiceHandler Repository instance.
     *
     * @var App\Repository\ServiceHandlerInterface
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
     * @param App\Repository\ServiceHandlerInterface $repository
     * @param \League\Tactician\CommandBus           $commandBus
     * @param App\Factory\Command                    $commandFactory
     *
     * @return void
     */
    public function __construct(
        ServiceHandlerInterface $repository,
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
     * @apiEndpointResponse 200 schema/service-handlers/listAll.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function listAll(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $company  = $request->getAttribute('company');
        $entities = $this->repository->getAllByCompanyId($company->id);

        $body = [
            'data'    => $entities->toArray(),
            'updated' => (
                $entities->isEmpty() ? time() : max($entities->max('created_at'), $entities->max('updated_at'))
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
     * Retrieves one Service handler of the acting Company.
     *
     * @apiEndpointResponse 200 schema/services-handlers/getOne.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getOne(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $company          = $request->getAttribute('company');
        $serviceHandlerId = (int) $request->getAttribute('decodedServiceHandlerId');

        $entity = $this->repository->findOne($company->id, $serviceHandlerId);

        $body = [
            'data' => $entity->toArray()
        ];

        $command = $this->commandFactory->create('ResponseDispatch');
        $command
            ->setParameter('request', $request)
            ->setParameter('response', $response)
            ->setParameter('body', $body);

        return $this->commandBus->handle($command);
    }

    /**
     * Creates a new ServiceHandler for the acting Company.
     *
     * @apiEndpointRequiredParam    body    int     service_id      Service's id.
     * @apiEndpointRequiredParam    body    array   listens         Service handler's listens property.
     *
     * @apiEndpointResponse 201 schema/services-handlers/createNew.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function createNew(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $company = $request->getAttribute('company');

        $command = $this->commandFactory->create('ServiceHandler\\CreateNew');
        $command
            ->setParameters($request->getParsedBody())
            ->setParameter('companyId', $company->id);

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
     * @apiEndpointResponse 200 schema/services-handlers/deleteAll.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function deleteAll(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $company = $request->getAttribute('company');

        $command = $this->commandFactory->create('ServiceHandler\\DeleteAll');
        $command->setParameter('companyId', $company->id);

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
     * Deletes one Service handler of the acting Company.
     *
     * @apiEndpointResponse 200 schema/services-handlers/deleteOne.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function deleteOne(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $company          = $request->getAttribute('company');
        $serviceHandlerId = $request->getAttribute('decodedServiceHandlerId');

        $command = $this->commandFactory->create('ServiceHandler\\DeleteOne');
        $command
            ->setParameter('companyId', $company->id)
            ->setParameter('serviceHandlerId', $serviceHandlerId);

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
     * Updates one Service handler of the acting Company.
     *
     * @apiEndpointParam    body    string      name            Service handler's name.
     * @apiEndpointParam    body    array      listens          Service handler's listens.
     *
     * @apiEndpointResponse 200 schema/services-handlers/updateOne.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function updateOne(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $company          = $request->getAttribute('company');
        $serviceHandlerId = $request->getAttribute('decodedServiceHandlerId');

        $command = $this->commandFactory->create('ServiceHandler\\UpdateOne');
        $command
            ->setParameters($request->getParsedBody())
            ->setParameter('serviceHandlerId', $serviceHandlerId)
            ->setParameter('companyId', $company->id);

        $entity = $this->commandBus->handle($command);

        $body = [
            'data' => $entity->toArray()
        ];

        $command = $this->commandFactory->create('ResponseDispatch');
        $command
            ->setParameter('request', $request)
            ->setParameter('response', $response)
            ->setParameter('body', $body);

        return $this->commandBus->handle($command);
    }
}
