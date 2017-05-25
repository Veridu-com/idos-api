<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Controller;

use App\Factory\Command;
use App\Repository\HandlerServiceInterface;
use League\Tactician\CommandBus;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Handles requests to /companies/{companySlug}/handler-services and /companies/{companySlug}/handler-services/{handlerServiceId}.
 */
class HandlerServices implements ControllerInterface {
    /**
     * HandlerService Repository instance.
     *
     * @var \App\Repository\HandlerServiceInterface
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
     * @var \App\Factory\Command
     */
    private $commandFactory;

    /**
     * Class constructor.
     *
     * @param \App\Repository\HandlerServiceInterface $repository
     * @param \League\Tactician\CommandBus            $commandBus
     * @param \App\Factory\Command                    $commandFactory
     *
     * @return void
     */
    public function __construct(
        HandlerServiceInterface $repository,
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
     * @apiEndpointResponse 200 schema/handlerServices/listAll.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @see \App\Repository\DBService::getAllByCompanyId
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function listAll(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $handlerId = $request->getAttribute('decodedHandlerId');

        $entities = $this->repository->getByHandlerId($handlerId, $request->getQueryParams());

        $body = [
            'data'    => $entities->toArray(),
            'updated' => (
                $entities->isEmpty() ? time() : max($entities->max('createdAt'), $entities->max('updatedAt'))
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
     * @apiEndpointResponse 200 schema/handlerService/getOne.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @see \App\Repository\DBService::findOne
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getOne(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $handlerServiceId = (int) $request->getAttribute('decodedHandlerServiceId');

        $entity = $this->repository->find($handlerServiceId);

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
     * Creates a new HandlerService for the acting Company.
     *
     * @apiEndpointRequiredParam    body    int     service_id   1325   Service's id.
     * @apiEndpointRequiredParam    body    array   listens     ['source.add.facebook']   Service handler's listens property.
     *
     * @apiEndpointResponse 201 schema/handlerService/createNew.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @see \App\Handler\HandlerService::handleCreateNew
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function createNew(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $company   = $request->getAttribute('targetCompany');
        $identity  = $request->getAttribute('identity');
        $handlerId = $request->getAttribute('decodedHandlerId');

        $command = $this->commandFactory->create('HandlerService\\CreateNew');
        $command
            ->setParameters($request->getParsedBody() ?: [])
            ->setParameter('handlerId', $handlerId)
            ->setParameter('identity', $identity)
            ->setParameter('company', $company);

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
     * Updates one Service handler of the acting Company.
     *
     * @apiEndpointRequiredParam    body    array      listens          Service handler's listens.
     *
     * @apiEndpointResponse 200 schema/handlerService/updateOne.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @see \App\Handler\HandlerService::handleUpdateOne
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function updateOne(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $company          = $request->getAttribute('targetCompany');
        $identity         = $request->getAttribute('identity');
        $handlerServiceId = $request->getAttribute('decodedHandlerServiceId');

        $command = $this->commandFactory->create('HandlerService\\UpdateOne');
        $command
            ->setParameters($request->getParsedBody() ?: [])
            ->setParameter('handlerServiceId', $handlerServiceId)
            ->setParameter('identity', $identity)
            ->setParameter('company', $company);

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

    /**
     * Deletes one Service handler of the acting Company.
     *
     * @apiEndpointResponse 200 schema/handlerService/deleteOne.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @see \App\Handler\HandlerService::handleDeleteOne
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function deleteOne(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $company          = $request->getAttribute('targetCompany');
        $identity         = $request->getAttribute('identity');
        $handlerServiceId = $request->getAttribute('decodedHandlerServiceId');

        $command = $this->commandFactory->create('HandlerService\\DeleteOne');
        $command
            ->setParameter('company', $company)
            ->setParameter('identity', $identity)
            ->setParameter('handlerServiceId', $handlerServiceId);

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
}
