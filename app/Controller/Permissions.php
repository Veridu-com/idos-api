<?php

/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Controller;

use App\Factory\Command;
use App\Repository\PermissionInterface;
use League\Tactician\CommandBus;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Handles requests to /companies/{companySlug}/permissions.
 */
class Permissions implements ControllerInterface {
    /**
     * Permission Repository instance.
     *
     * @var App\Repository\PermissionInterface
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
     * @param App\Repository\PermissionInterface $repository
     * @param \League\Tactician\CommandBus       $commandBus
     * @param App\Factory\Command                $commandFactory
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
     * @apiEndpointResponse 200 Permission[]
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function listAll(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $targetCompany    = $request->getAttribute('targetCompany');
        $permissions      = $this->repository->getAllByCompanyId($targetCompany->id);

        $body = [
            'data'    => $permissions->toArray(),
            // TODO: Discuss with Flavio if this "updated" makes sense.
            // Should a deletion refresh it? How?
            'updated' => (
                $permissions->isEmpty() ? time() : $permissions->max('created_at')
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
     * Lists all Permissions that belongs to the Target Company and has the given section.
     *
     * @apiEndpointRequiredParam path string section
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
        $targetCompany    = $request->getAttribute('targetCompany');
        $section          = $request->getAttribute('section');
        $permissions      = $this->repository->getAllByCompanyIdAndSection($targetCompany->id, $section);

        $body = [
            'data'    => $permissions->toArray(),
            'updated' => (
                $permissions->isEmpty() ? time() : $permissions->max('updated_at')
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
     * @apiEndpointRequiredParam path string companySlug
     * @apiEndpointRequiredParam path string routeName
     * @apiEndpointResponse 200 Permission
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getOne(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $targetCompany = $request->getAttribute('targetCompany');
        $routeName     = $request->getAttribute('routeName');
        $permission    = $this->repository->findOne($targetCompany->id, $routeName);

        $body = [
            'data'    => $permission->toArray()
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
     * @apiEndpointResponse 201 Permission
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function createNew(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $targetCompany = $request->getAttribute('targetCompany');

        $command = $this->commandFactory->create('Permission\\CreateNew');
        $command
            ->setParameters($request->getParsedBody())
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
     * Deletes all Permissions that belongs to the Target Company.
     *
     * @apiEndpointResponse 200 -
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function deleteAll(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $targetCompany = $request->getAttribute('targetCompany');

        $command = $this->commandFactory->create('Permission\\DeleteAll');
        $command->setParameter('companyId', $targetCompany->id);

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
     * Deletes one Permission of the Target Company based on path paramater routeName.
     *
     * @apiEndpointRequiredParam path string companySlug
     * @apiEndpointRequiredParam path string routeName
     * @apiEndpointResponse 200 -
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function deleteOne(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $targetCompany = $request->getAttribute('targetCompany');
        $routeName     = $request->getAttribute('routeName');

        $command = $this->commandFactory->create('Permission\\DeleteOne');
        $command
            ->setParameter('companyId', $targetCompany->id)
            ->setParameter('routeName', $routeName);

        $deleted = $this->commandBus->handle($command);
        $body    = [
            'status'  => $deleted === 1
        ];

        $statusCode = $body['status'] ? 200 : 404;

        $command = $this->commandFactory->create('ResponseDispatch');
        $command
            ->setParameter('statusCode', $statusCode)
            ->setParameter('request', $request)
            ->setParameter('response', $response)
            ->setParameter('body', $body);

        return $this->commandBus->handle($command);
    }
}
