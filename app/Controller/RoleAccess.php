<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace App\Controller;

use App\Factory\Command;
use App\Repository\RoleAccessInterface;
use Jenssegers\Optimus\Optimus;
use League\Tactician\CommandBus;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Handles requests to /access/roles and /access/roles/{roleName}.
 */
class RoleAccess implements ControllerInterface {
    /**
     * RoleAccess Repository instance.
     *
     * @var App\Repository\RoleAccessInterface
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
     * @param App\Repository\RoleAccessInterface $repository
     * @param \League\Tactician\CommandBus    $commandBus
     * @param App\Factory\Command             $commandFactory
     *
     * @return void
     */
    public function __construct(
        RoleAccessInterface $repository,
        CommandBus $commandBus,
        Command $commandFactory,
        Optimus $optimus
    ) {
        $this->repository     = $repository;
        $this->commandBus     = $commandBus;
        $this->commandFactory = $commandFactory;
        $this->optimus        = $optimus;
    }
    /**
     * List all child RoleAccess that belongs to the Acting RoleAccess.
     *
     * @apiEndpointParam query string after 2016-01-01|1070-01-01 Initial RoleAccess creation date (lower bound)
     * @apiEndpointParam query string before 2016-01-31|2016-12-31 Final RoleAccess creation date (upper bound)
     * @apiEndpointParam query int page 10|1 Current page
     * @apiEndpointResponse 200 schema/company/listAll.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function listAll(ServerRequestInterface $request, ResponseInterface $response) {
        $actingUser = $request->getAttribute('actingUser');
        $entities = $this->repository->findByIdentity($actingUser->identity_id);

        $body = [
            'data'    => $entities->toArray()
        ];

        $command = $this->commandFactory->create('ResponseDispatch');
        $command
            ->setParameter('request', $request)
            ->setParameter('response', $response)
            ->setParameter('body', $body);

        return $this->commandBus->handle($command);
    }

    /**
     * Retrieves role access defined to certain role
     *
     * @apiEndpointResponse 200 schema/company/listAllFromRole.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @throws App\Exception\NotFound
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getOne(ServerRequestInterface $request, ResponseInterface $response) {
        $actingUser = $request->getAttribute('actingUser');
        $role = $request->getAttribute('roleName');
        $resource = $request->getAttribute('resource');

        $entities = $this->repository->findOne($actingUser->identity_id, $role, $resource);

        $body = [
            'data'    => $entities->toArray()
        ];

        $command = $this->commandFactory->create('ResponseDispatch');
        $command
            ->setParameter('request', $request)
            ->setParameter('response', $response)
            ->setParameter('body', $body);

        return $this->commandBus->handle($command);
    }
    /**
     * Retrieves role access defined to certain role
     *
     * @apiEndpointResponse 200 schema/company/listAllFromRole.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @throws App\Exception\NotFound
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function listAllFromRole(ServerRequestInterface $request, ResponseInterface $response) {
        $actingUser = $request->getAttribute('actingUser');
        $role = $request->getAttribute('roleName');

        $entities = $this->repository->findByIdentityAndRole($actingUser->identity_id, $role);

        $body = [
            'data'    => $entities->toArray()
        ];

        $command = $this->commandFactory->create('ResponseDispatch');
        $command
            ->setParameter('request', $request)
            ->setParameter('response', $response)
            ->setParameter('body', $body);

        return $this->commandBus->handle($command);
    }

    /**
     * Creates a new child RoleAccess for the Acting RoleAccess.
     *
     * @apiEndpointRequiredParam body string name NewCo. RoleAccess name
     * @apiEndpointResponse 201 schema/company/createNew.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function createNew(ServerRequestInterface $request, ResponseInterface $response) {
        $actingUser = $request->getAttribute('actingUser');
        $body = $request->getParsedBody();

        $command = $this->commandFactory->create('RoleAccess\\CreateNew');
        $command
            ->setParameters($request->getParsedBody())
            ->setParameter('identityId', $actingUser->identityId);
        $company = $this->commandBus->handle($command);

        $body = [
            'data' => $company->toArray()
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
     * Deletes all child RoleAccess that belongs to the Acting RoleAccess.
     *
     * @apiEndpointResponse 200 schema/company/deleteAll.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function deleteAll(ServerRequestInterface $request, ResponseInterface $response) {
        $actingUser = $request->getAttribute('actingUser');

        $command = $this->commandFactory->create('RoleAccess\\DeleteAll');
        $command->setParameter('identityId', $actingUser->identityId);

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
     * Deletes the Target RoleAccess, a child of the Acting RoleAccess.
     *
     * @apiEndpointResponse 200 schema/company/deleteOne.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @throws App\Exception\NotFound
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function deleteOne(ServerRequestInterface $request, ResponseInterface $response) {
        $actingUser = $request->getAttribute('actingUser');
        $role = $request->getAttribute('roleName');
        $resource = $request->getAttribute('resource');

        $command = $this->commandFactory->create('RoleAccess\\DeleteOne');
        $command->setParameter('identityId', $actingUser->identityId);
        $command->setParameter('role', $role);
        $command->setParameter('resource', $resource);

        $deleted = $this->commandBus->handle($command);

        $body = [
            'status' => (bool) $deleted
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
     * @apiEndpointRequiredParam body string name NewName New RoleAccess name
     * @apiEndpointResponse 200 schema/company/updateOne.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @throws App\Exception\NotFound
     *
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @see App\Command\RoleAccess\UpdateOne
     */
    public function updateOne(ServerRequestInterface $request, ResponseInterface $response) {
        $actingUser = $request->getAttribute('actingUser');

        $role = $request->getAttribute('roleName');
        $resource = $request->getAttribute('resource');
        $body = $request->getParsedBody();

        $command = $this->commandFactory->create('RoleAccess\\UpdateOne');
        $command
            ->setParameters($request->getParsedBody())
            ->setParameter('role', $role)
            ->setParameter('resource', $resource)
            ->setParameter('identityId', $actingUser->identityId);
        $company = $this->commandBus->handle($command);

        $body = [
            'data' => $company->toArray()
        ];

        $command = $this->commandFactory->create('ResponseDispatch');
        $command
            ->setParameter('request', $request)
            ->setParameter('response', $response)
            ->setParameter('body', $body);

        return $this->commandBus->handle($command);

    }

}
