<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Controller;

use App\Factory\Command;
use App\Repository\ServiceInterface;
use League\Tactician\CommandBus;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Handles requests to /services.
 */
class Services implements ControllerInterface {
    /**
     * Service Repository instance.
     *
     * @var \App\Repository\ServiceInterface
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
     * @param \App\Repository\ServiceInterface $repository
     * @param \League\Tactician\CommandBus     $commandBus
     * @param \App\Factory\Command             $commandFactory
     *
     * @return void
     */
    public function __construct(
        ServiceInterface $repository,
        CommandBus $commandBus,
        Command $commandFactory
    ) {
        $this->repository     = $repository;
        $this->commandBus     = $commandBus;
        $this->commandFactory = $commandFactory;
    }

    /**
     * Lists all Services that are visible to the acting Company.
     *
     * @apiEndpointResponse 200 schema/service/listAll.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function listAll(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $company  = $request->getAttribute('targetCompany');
        $entities = $this->repository->getByCompany($company, $request->getQueryParams());

        $body = [
            'data'    => $entities->toArray(),
            'updated' => (
                $entities->isEmpty() ? time() : max($entities->max('updated_at'), $entities->max('created_at'))
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
     * Retrieves one Service.
     *
     * @apiEndpointResponse 200 schema/service/getOne.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getOne(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $company   = $request->getAttribute('targetCompany');
        $serviceId = (int) $request->getAttribute('decodedServiceId');

        $entity = $this->repository->findOne($serviceId, $company);

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
     * Creates a new Service for the acting Company.
     *
     * @apiEndpointRequiredParam    body    string    name  name             Service's name.
     * @apiEndpointRequiredParam    body    string     url  http://service-url.com             Service's url.
     * @apiEndpointParam            body    int        access 1           Service's access.
     * @apiEndpointParam            body    bool       enabled  true         Service's enabled.
     * @apiEndpointParam            body    array       listens 'source.add.facebook'         Service's listens.
     * @apiEndpointParam            body    array       triggers 'source.scraper.facebook.finished'       Service's triggers.
     * @apiEndpointRequiredParam    body    string      auth_username idos   Service's authUsername.
     * @apiEndpointRequiredParam    body    string      auth_password  secret   Service's authPassword.
     *
     * @apiEndpointResponse 201 schema/service/createNew.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @see \App\Entity\Service
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function createNew(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $company = $request->getAttribute('targetCompany');

        $command = $this->commandFactory->create('Service\\CreateNew');
        $command
            ->setParameters($request->getParsedBody() ?: [])
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
     * Updates one Service of the acting Company based on path paramaters service id.
     *
     * @apiEndpointRequiredParam    body    string    name  name             Service's name.
     * @apiEndpointRequiredParam    body    string     url  http
     * @apiEndpointParam            body    int        access 1           Service's access.
     * @apiEndpointParam            body    bool       enabled  true         Service's enabled.
     * @apiEndpointParam            body    array       listens 'source.add.facebook'         Service's listens.
     * @apiEndpointParam            body    array       triggers 'source.scraper.facebook.finished'       Service's triggers.
     * @apiEndpointRequiredParam    body    string      auth_username idos   Service's authUsername.
     * @apiEndpointRequiredParam    body    string      auth_password  secret   Service's authPassword.
     * @apiEndpointResponse 200 schema/service/updateOne.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function updateOne(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $company   = $request->getAttribute('targetCompany');
        $serviceId = $request->getAttribute('decodedServiceId');

        $command = $this->commandFactory->create('Service\\UpdateOne');
        $command
            ->setParameters($request->getParsedBody() ?: [])
            ->setParameter('serviceId', $serviceId)
            ->setParameter('company', $company);

        $entity = $this->commandBus->handle($command);

        $body = [
            'data'    => $entity->toArray(),
            'updated' => $entity->updatedAt
        ];

        $command = $this->commandFactory->create('ResponseDispatch');
        $command
            ->setParameter('request', $request)
            ->setParameter('response', $response)
            ->setParameter('body', $body);

        return $this->commandBus->handle($command);
    }

    /**
     * Deletes one Service of the acting Company based on path paramaters service id.
     *
     * @apiEndpointResponse 200 schema/service/deleteOne.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function deleteOne(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $company   = $request->getAttribute('targetCompany');
        $serviceId = $request->getAttribute('decodedServiceId');

        $command = $this->commandFactory->create('Service\\DeleteOne');
        $command
            ->setParameter('company', $company)
            ->setParameter('serviceId', $serviceId);

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
     * Deletes all Services that belongs to the acting Company.
     *
     * @apiEndpointResponse 200 schema/service/deleteAll.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function deleteAll(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $company = $request->getAttribute('targetCompany');

        $command = $this->commandFactory->create('Service\\DeleteAll');
        $command->setParameter('company', $company);

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
}
