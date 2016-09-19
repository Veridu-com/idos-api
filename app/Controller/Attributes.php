<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Controller;

use App\Entity\User;
use App\Factory\Command;
use App\Repository\AttributeInterface;
use League\Tactician\CommandBus;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Handles requests to /profiles/{userName}/attribute.
 */
class Attributes implements ControllerInterface {
    /**
     * Attribute Repository instance.
     *
     * @var App\Repository\AttributeInterface
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
     * @param App\Repository\AttributeInterface $repository
     * @param \League\Tactician\CommandBus      $commandBus
     * @param App\Factory\Command               $commandFactory
     *
     * @return void
     */
    public function __construct(
        AttributeInterface $repository,
        CommandBus $commandBus,
        Command $commandFactory
    ) {
        $this->repository     = $repository;
        $this->commandBus     = $commandBus;
        $this->commandFactory = $commandFactory;
    }

    /**
     * Retrieve a complete list of attributes of the given user.
     *
     * @apiEndpointURIFragment string userName usr001
     * @apiEndpointResponse 200 schema/attribute/listAll.json
     *
     * @param \Psr\ServerRequestInterface $request
     * @param \Psr\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function listAll(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $user    = $request->getAttribute('targetUser');
        $service = $request->getAttribute('service');

        $entities = $this->repository->findBy(['user_id' => $user->id], $request->getQueryParams());

        $body = [
            'data'    => $entities->toArray(),
            'updated' => (
                $entities->isEmpty() ? time() : max($entities->max('updatedAt'), $entities->max('createdAt'))
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
     * Created a new attribute data for a given source.
     *
     * @apiEndpointResponse 201 schema/attribute/attributeEntity.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function createNew(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $user    = $request->getAttribute('targetUser');
        $service = $request->getAttribute('service');

        $command = $this->commandFactory->create('Attribute\\CreateNew');
        $command
            ->setParameters($request->getParsedBody())
            ->setParameter('user', $user)
            ->setParameter('service', $service);

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
     * Retrieves a attribute data from the given source.
     *
     * @apiEndpointURIFragment string userName usr001
     * @apiEndpointParam query string attributeName firstName
     * @apiEndpointResponse 200 schema/attribute/attributeEntity.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getOne(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $user    = $request->getAttribute('targetUser');
        $service = $request->getAttribute('service');
        $name    = $request->getAttribute('attributeName');

        $entities = $this->repository->findBy(
            [
            'user_id' => $user->id,
            'name'    => $name
            ]
        );

        $body = [
            'data' => $entities->toArray()
        ];

        $command = $this->commandFactory->create('ResponseDispatch');
        $command
            ->setParameter('request', $request)
            ->setParameter('response', $response)
            ->setParameter('body', $body);

        return $this->commandBus->handle($command);
    }

    /**
     * Deletes all attribute data from a given source.
     *
     * @apiEndpointResponse 200 schema/attribute/deleteAll.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function deleteAll(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $user    = $request->getAttribute('targetUser');
        $service = $request->getAttribute('service');

        $command = $this->commandFactory->create('Attribute\\DeleteAll');
        $command
            ->setParameter('user', $user)
            ->setParameter('service', $service)
            ->setParameter('queryParams', $request->getQueryParams());

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
     * Deletes a attribute data from a given source.
     *
     * @apiEndpointURIFragment string userName usr001
     * @apiEndpointParam query string attributeName firstName
     * @apiEndpointResponse 200 schema/attribute/deleteOne.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function deleteOne(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $user    = $request->getAttribute('targetUser');
        $service = $request->getAttribute('service');
        $name    = $request->getAttribute('attributeName');

        $command = $this->commandFactory->create('Attribute\\DeleteOne');
        $command
            ->setParameter('user', $user)
            ->setParameter('service', $service)
            ->setParameter('name', $name);

        $body = [
            'deleted' => $this->commandBus->handle($command)
        ];

        $statusCode = ($body['deleted'] > 0) ? 200 : 404;
        $command    = $this->commandFactory->create('ResponseDispatch');
        $command
            ->setParameter('request', $request)
            ->setParameter('response', $response)
            ->setParameter('body', $body);

        return $this->commandBus->handle($command);
    }
}
