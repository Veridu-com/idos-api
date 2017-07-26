<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Controller\User;

use App\Controller\ControllerInterface;
use App\Factory\Command;
use App\Repository\RepositoryInterface;
use League\Tactician\CommandBus;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Handles requests to /access/roles.
 */
class RoleAccess implements ControllerInterface {
    /**
     * RoleAccess Repository instance.
     *
     * @var \App\Repository\RepositoryInterface
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
     * @param \App\Repository\RepositoryInterface $repository
     * @param \League\Tactician\CommandBus        $commandBus
     * @param \App\Factory\Command                $commandFactory
     *
     * @return void
     */
    public function __construct(
        RepositoryInterface $repository,
        CommandBus $commandBus,
        Command $commandFactory
    ) {
        $this->repository     = $repository;
        $this->commandBus     = $commandBus;
        $this->commandFactory = $commandFactory;
    }

    /**
     * List all child RoleAccess that belongs to the acting User.
     *
     * @apiEndpointResponse 200 schema/access/roles/listAll.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function listAll(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $user     = $request->getAttribute('user');
        $entities = $this->repository->findByIdentity($user->identity_id);

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
     * Retrieves role access defined to certain role and resource for the acting User.
     *
     * @apiEndpointURIFragment     int roleAccessId 5319
     *
     * @apiEndpointResponse 200 schema/access/roles/getOne.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @see \App\Repository\DBRoleAccess::findOne
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getOne(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $user                = $request->getAttribute('user');
        $decodedRoleAccessId = $request->getAttribute('decodedRoleAccessId');

        $entity = $this->repository->findOne($user->identityId, $decodedRoleAccessId);

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
     * Creates a new RoleAccess for the acting User.
     *
     * @apiEndpointRequiredParam body       string role     owner
     * @apiEndpointRequiredParam body       string resource candidates:listAll
     * @apiEndpointRequiredParam body       int    access   3124
     *
     * @apiEndpointResponse 201 schema/access/roles/createNew.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @see \App\Handler\RoleAccess::handleCreateNew
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function createNew(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $user = $request->getAttribute('user');
        $body = $request->getParsedBody();

        $command = $this->commandFactory->create('RoleAccess\CreateNew');
        $command
            ->setParameters($request->getParsedBody() ?: [])
            ->setParameter('identityId', $user->identityId);
        $entity = $this->commandBus->handle($command);

        $body = [
            'data' => $entity->toArray()
        ];

        $command = $this->commandFactory->create('ResponseDispatch');
        $command
            ->setParameter('request', $request)
            ->setParameter('response', $response)
            ->setParameter('body', $body)
            ->setParameter('statusCode', 201);

        return $this->commandBus->handle($command);
    }

    /**
     * Deletes all RoleAccess registers that belongs to the acting User.
     *
     * @apiEndpointResponse 200 schema/access/roles/deleteAll.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @see \App\Handler\RoleAccess::handleDeleteAll
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function deleteAll(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $user = $request->getAttribute('user');

        $command = $this->commandFactory->create('RoleAccess\DeleteAll');
        $command->setParameter('identityId', $user->identityId);

        $deleted = $this->commandBus->handle($command);

        $body = [
            'deleted' => $deleted
        ];

        $command = $this->commandFactory->create('ResponseDispatch');
        $command
            ->setParameter('request', $request)
            ->setParameter('response', $response)
            ->setParameter('body', $body);

        return $this->commandBus->handle($command);
    }

    /**
     * Deletes a RoleAccess of the acting User.
     *
     * @apiEndpointResponse 200 schema/access/roles/deleteOne.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @see \App\Handler\RoleAccess::handleDeleteOne
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function deleteOne(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $user                = $request->getAttribute('user');
        $decodedRoleAccessId = $request->getAttribute('decodedRoleAccessId');

        $command = $this->commandFactory->create('RoleAccess\DeleteOne');
        $command->setParameter('identityId', $user->identityId);
        $command->setParameter('roleAccessId', $decodedRoleAccessId);

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
     * Updates the Target RoleAccess, a child of the Acting RoleAccess.
     *
     * @apiEndpointRequiredParam body       int roleAccess              The access value.
     *
     * @apiEndpointResponse 200 schema/access/roles/updateOne.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @see \App\Handler\RoleAccess::handleUpdateOne
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function updateOne(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $user                = $request->getAttribute('user');
        $decodedRoleAccessId = $request->getAttribute('decodedRoleAccessId');

        $command = $this->commandFactory->create('RoleAccess\UpdateOne');
        $command
            ->setParameters($request->getParsedBody() ?: [])
            ->setParameter('roleAccessId', $decodedRoleAccessId)
            ->setParameter('identityId', $user->identityId);

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
