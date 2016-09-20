<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Controller\Profile;

use App\Factory\Command;
use App\Repository\UserInterface;
use App\Repository\Profile\WarningInterface;
use League\Tactician\CommandBus;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use App\Controller\ControllerInterface;

/**
 * Handles requests to companies/{companySlug}/profiles/{userId}/warnings.
 */
class Warnings implements ControllerInterface {
    /**
     * Warning Repository instance.
     *
     * @var App\Repository\Profile\WarningInterface
     */
    private $repository;
    /**
     * User Repository instance.
     *
     * @var App\Repository\UserInterface
     */
    private $userRepository;
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
     * @param App\Repository\Profile\WarningInterface $repository
     * @param App\Repository\UserInterface    $userRepository
     * @param \League\Tactician\CommandBus    $commandBus
     * @param App\Factory\Command             $commandFactory
     *
     * @return void
     */
    public function __construct(
        WarningInterface $repository,
        UserInterface $userRepository,
        CommandBus $commandBus,
        Command $commandFactory
    ) {
        $this->repository     = $repository;
        $this->userRepository = $userRepository;
        $this->commandBus     = $commandBus;
        $this->commandFactory = $commandFactory;
    }

    /**
     * Lists all Warnings that belongs to the given user.
     *
     * @apiEndpointParam query int page 10|1 Current page
     * @apiEndpointResponse 200 schema/warning/listAll.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @see App\Repository\DBWarning::findBy
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
     * Retrieves one Warning of the User.
     *
     * @apiEndpointResponse 200 schema/warning/getOne.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @see App\Repository\DBWarning::findOneBySlug
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getOne(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $user    = $request->getAttribute('targetUser');
        $service = $request->getAttribute('service');
        $slug    = $request->getAttribute('warningSlug');

        $entity = $this->repository->findOneBySlug($user->id, $service->id, $slug);

        $body = [
            'data'    => $entity->toArray(),
            'updated' => $entity->updated_at
        ];

        $command = $this->commandFactory->create('ResponseDispatch');
        $command
            ->setParameter('request', $request)
            ->setParameter('response', $response)
            ->setParameter('body', $body);

        return $this->commandBus->handle($command);
    }

    /**
     * Creates a new Feture for the given user.
     *
     * @apiEndpointRequiredParam body string name warning test Warning name
     * @apiEndpointRequiredParam body string reference firstName Warning reference
     * @apiEndpointResponse 201 schema/warning/createNew.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @see App\Handler\Warning::handleCreateNew
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function createNew(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $user    = $request->getAttribute('targetUser');
        $service = $request->getAttribute('service');

        $command = $this->commandFactory->create('Warning\\CreateNew');
        $command
            ->setParameters($request->getParsedBody())
            ->setParameter('user', $user)
            ->setParameter('service', $service);

        $warning = $this->commandBus->handle($command);

        $body = [
            'status' => true,
            'data'   => $warning->toArray()
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
     * Deletes all Warnings that belongs to the User.
     *
     * @apiEndpointResponse 200 schema/warning/deleteAll.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @see App\Handler\Warning::handleDeleteAll
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function deleteAll(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $user    = $request->getAttribute('targetUser');
        $service = $request->getAttribute('service');

        $command = $this->commandFactory->create('Warning\\DeleteAll');
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
     * Deletes one Warning of the User.
     *
     * @apiEndpointResponse 200 schema/warning/deleteOne.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @see App\Handler\Warning::handleDeleteOne
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function deleteOne(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $user    = $request->getAttribute('targetUser');
        $service = $request->getAttribute('service');
        $slug    = $request->getAttribute('warningSlug');

        $command = $this->commandFactory->create('Warning\\DeleteOne');
        $command
            ->setParameter('user', $user)
            ->setParameter('service', $service)
            ->setParameter('slug', $slug);

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
