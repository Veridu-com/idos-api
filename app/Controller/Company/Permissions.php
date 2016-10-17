<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Controller\Company;

use App\Controller\ControllerInterface;
use App\Factory\Command;
use App\Repository\Company\PermissionInterface;
use League\Tactician\CommandBus;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Handles requests to /companies/{companySlug}/permissions and /companies/{companySlug}/permissions/{routeName}.
 */
class Permissions implements ControllerInterface {
    /**
     * Permission Repository instance.
     *
     * @var \App\Repository\Company\PermissionInterface
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
     * @param \App\Repository\Company\PermissionInterface $repository
     * @param \League\Tactician\CommandBus                $commandBus
     * @param \App\Factory\Command                        $commandFactory
     *
     * @return void
     */
    public function __construct(
        PermissionInterface $repository,
        CommandBus $commandBus,
        Command $commandFactory
    ) {
        $this->repository     = $repository;
        $this->commandBus     = $commandBus;
        $this->commandFactory = $commandFactory;
    }

    /**
     * Lists all Permissions that belongs to the Target Company.
     *
     * @apiEndpointParam query int page Current page
     * @apiEndpointResponse 200 schema/permission/listAll.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @see \App\Repository\DBPermission::getAllByCompanyId
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function listAll(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $targetCompany = $request->getAttribute('targetCompany');
        $permissions   = $this->repository->getByCompanyId($targetCompany->id);

        $body = [
            'data'    => $permissions->toArray(),
            'updated' => (
                $permissions->isEmpty() ? time() : $permissions->max('createdAt')
            )
        ];

        $command = $this->commandFactory->create('ResponseDispatch');
        $command
            ->setParameter('request', $request)
            ->setParameter('response', $response)
            ->setParameter('body', $body);

        return $this->commandBus->handle($command);
    }

    // FIXME REMOVE listAllFromSection - If required, it should be a filter on listAll

    /**
     * Lists all Permissions that belongs to the Target Company and has the given section.
     *
     * @apiEndpointURIFragment string section xxx
     *
     * @apiEndpointParam query int page Current page
     * @apiEndpointResponse 200 Permission[]
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function listAllFromSection(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $targetCompany = $request->getAttribute('targetCompany');
        $section       = $request->getAttribute('section');
        $permissions   = $this->repository->getAllByCompanyIdAndSection($targetCompany->id, $section);

        $body = [
            'data'    => $permissions->toArray(),
            'updated' => (
                $permissions->isEmpty() ? time() : $permissions->max('updatedAt')
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
     * Retrieves one Permission of the Target Company based on path paramaters routeName.
     *
     * @apiEndpointResponse 200 schema/permission/getOne.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @see \App\Repository\DBPermission::findOne
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getOne(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $targetCompany = $request->getAttribute('targetCompany');
        $routeName     = $request->getAttribute('routeName');
        $permission    = $this->repository->findOne($targetCompany->id, $routeName);

        $body = [
            'data' => $permission->toArray()
        ];

        $command = $this->commandFactory->create('ResponseDispatch');
        $command
            ->setParameter('request', $request)
            ->setParameter('response', $response)
            ->setParameter('body', $body);

        return $this->commandBus->handle($command);
    }

    /**
     * Creates a new Permission for the Target Company.
     *
     * @apiEndpointResponse 201 schema/permission/createNew.json
     * @apiEndpointRequiredParam body string routeName attribute:listAll A valid route name
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @see \App\Handler\Permission::handleCreateNew
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function createNew(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $targetCompany = $request->getAttribute('targetCompany');

        $command = $this->commandFactory->create('Company\\Permission\\CreateNew');
        $command
            ->setParameters($request->getParsedBody() ?: [])
            ->setParameter('companyId', $targetCompany->id);

        $permission = $this->commandBus->handle($command);

        $body = [
            'status' => true,
            'data'   => $permission->toArray()
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
     * Deletes one Permission of the Target Company based on path paramater routeName.
     *
     * @apiEndpointURIFragment string companySlug veridu-ltd
     * @apiEndpointURIFragment string routeName company:listAll
     * @apiEndpointResponse 200 schema/permission/deleteOne.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @see \App\Handler\Permission::handleDeleteOne
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function deleteOne(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $targetCompany = $request->getAttribute('targetCompany');
        $routeName     = $request->getAttribute('routeName');

        $command = $this->commandFactory->create('Company\\Permission\\DeleteOne');
        $command
            ->setParameter('companyId', $targetCompany->id)
            ->setParameter('routeName', $routeName);

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
