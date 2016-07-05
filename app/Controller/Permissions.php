<?php
/**
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

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
     * @param \League\Tactician\CommandBus    $commandBus
     * @param App\Factory\Command             $commandFactory
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
    public function listAll(ServerRequestInterface $request, ResponseInterface $response) {
        $targetCompany = $request->getAttribute('targetCompany');
        $permissions      = $this->repository->getAllByCompanyId($targetCompany->id);

        $body = [
            'data'    => $permissions->toArray(),
            'updated' => (
                $permissions->isEmpty() ? time() : strtotime($permissions->max('updated_at'))
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
    public function listAllFromSection(ServerRequestInterface $request, ResponseInterface $response) {
        $targetCompany = $request->getAttribute('targetCompany');
        $section       = $request->getAttribute('section');
        $permissions      = $this->repository->getAllByCompanyIdAndSection($targetCompany->id, $section);

        $body = [
            'data'    => $permissions->toArray(),
            'updated' => (
                $permissions->isEmpty() ? time() : strtotime($permissions->max('updated_at'))
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
     * Retrieves one Permission of the Target Company based on path paramaters section and property.
     *
     * @apiEndpointRequiredParam path string companySlug
     * @apiEndpointRequiredParam path string section
     * @apiEndpointRequiredParam path string property
     * @apiEndpointResponse 200 Permission
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getOne(ServerRequestInterface $request, ResponseInterface $response) {
        $targetCompany = $request->getAttribute('targetCompany');
        $section       = $request->getAttribute('section');
        $propName      = $request->getAttribute('property');
        $permission       = $this->repository->findOne($targetCompany->id, $section, $propName);

        $body = [
            'data'    => $permission->toArray(),
            'updated' => strtotime($permission->updated_at)
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
    public function createNew(ServerRequestInterface $request, ResponseInterface $response) {
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
    public function deleteAll(ServerRequestInterface $request, ResponseInterface $response) {
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
     * Deletes one Permission of the Target Company based on path paramaters section and property.
     *
     * @apiEndpointRequiredParam path string companySlug
     * @apiEndpointRequiredParam path string section
     * @apiEndpointRequiredParam path string property
     * @apiEndpointResponse 200 -
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function deleteOne(ServerRequestInterface $request, ResponseInterface $response) {
        $targetCompany = $request->getAttribute('targetCompany');
        $section       = $request->getAttribute('section');
        $property      = $request->getAttribute('property');

        $command = $this->commandFactory->create('Permission\\DeleteOne');
        $command
            ->setParameter('companyId', $targetCompany->id)
            ->setParameter('section', $section)
            ->setParameter('property', $property);

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
     * Updates one Permission of the Target Company based on path paramaters section and property.
     *
     * @apiEndpointRequiredParam path string companySlug
     * @apiEndpointRequiredParam path string section
     * @apiEndpointRequiredParam path string property
     * @apiEndpointResponse 200 Permission
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function updateOne(ServerRequestInterface $request, ResponseInterface $response) {
        $targetCompany = $request->getAttribute('targetCompany');
        $section       = $request->getAttribute('section');
        $propName      = $request->getAttribute('property');

        $command = $this->commandFactory->create('Permission\\UpdateOne');
        $command
            ->setParameters($request->getParsedBody())
            ->setParameter('sectionNameId', $section)
            ->setParameter('propNameId', $propName)
            ->setParameter('companyId', $targetCompany->id);

        $permission = $this->commandBus->handle($command);

        $body = [
            'data'    => $permission->toArray(),
            'updated' => strtotime($permission->updated_at)
        ];

        $command = $this->commandFactory->create('ResponseDispatch');
        $command
            ->setParameter('request', $request)
            ->setParameter('response', $response)
            ->setParameter('body', $body);

        return $this->commandBus->handle($command);
    }

}
