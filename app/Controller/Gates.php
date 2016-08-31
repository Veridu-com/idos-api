<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Controller;

use App\Factory\Command;
use App\Repository\GateInterface;
use App\Repository\UserInterface;
use League\Tactician\CommandBus;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Handles requests to /profiles/:userName/gates.
 */
class Gates implements ControllerInterface {
    /**
     * Setting Repository instance.
     *
     * @var App\Repository\GateInterface
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
     * @param App\Repository\GateInterface $repository
     * @param App\Repository\UserInterface $userRepository
     * @param \League\Tactician\CommandBus $commandBus
     * @param App\Factory\Command          $commandFactory
     *
     * @return void
     */
    public function __construct(
        GateInterface $repository,
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
     * Lists all Gates that belongs to the given user.
     *
     * @apiEndpointParam query int page 10|1 Current page
     * @apiEndpointResponse 200 schema/gate/listAll.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function listAll(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $user   = $request->getAttribute('targetUser');
        $result = $this->repository->getAllByUserId($user->id, $request->getQueryParams());

        $entities = $result['collection'];

        $body = [
            'data'       => $entities->toArray(),
            'pagination' => $result['pagination'],
            'updated'    => (
                $entities->isEmpty() ? null : max($entities->max('updatedAt'), $entities->max('createdAt'))
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
     * Retrieves one Gate of the User.
     *
     * @apiEndpointResponse 200 schema/gate/getOne.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getOne(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $user     = $request->getAttribute('targetUser');
        $gateSlug = $request->getAttribute('gateSlug');

        $gate = $this->repository->findByUserIdAndSlug($user->id, $gateSlug);

        $body = [
            'data'    => $gate->toArray(),
            'updated' => $gate->updated_at
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
     * @apiEndpointRequiredParam body string name XYZ Gate name
     * @apiEndpointRequiredParam body boolean pass ZYX Gate pass
     * @apiEndpointResponse 201 schema/gate/createNew.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function createNew(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $user = $request->getAttribute('targetUser');

        $command = $this->commandFactory->create('Gate\\CreateNew');
        $command
            ->setParameters($request->getParsedBody())
            ->setParameters(['userId' => $user->id]);

        $gate = $this->commandBus->handle($command);

        $body = [
            'status' => true,
            'data'   => $gate->toArray()
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
     * Deletes all Gates that belongs to the User.
     *
     * @apiEndpointResponse 200 schema/setting/deleteAll.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function deleteAll(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $user = $request->getAttribute('targetUser');

        $command = $this->commandFactory->create('Gate\\DeleteAll');
        $command->setParameter('userId', $user->id);

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
     * Deletes one Gate of the User.
     *
     * @apiEndpointResponse 200 schema/gate/deleteOne.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function deleteOne(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $user     = $request->getAttribute('targetUser');
        $gateSlug = $request->getAttribute('gateSlug');

        $command = $this->commandFactory->create('Gate\\DeleteOne');
        $command->setParameter('userId', $user->id)
            ->setParameter('gateSlug', $gateSlug);

        $body = [
            'status' => (bool) $this->commandBus->handle($command)
        ];

        $command = $this->commandFactory->create('ResponseDispatch');
        $command
            ->setParameter('request', $request)
            ->setParameter('response', $response)
            ->setParameter('body', $body);

        return $this->commandBus->handle($command);
    }

    /**
     * Updates one Gate of the User.
     *
     * @apiEndpointRequiredParam body string name XYZ Gate name
     * @apiEndpointRequiredParam body boolean pass ZYX Gate pass
     * @apiEndpointResponse 200 schema/gate/updateOne.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function updateOne(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $user     = $request->getAttribute('targetUser');
        $gateSlug = $request->getAttribute('gateSlug');

        $command = $this->commandFactory->create('Gate\\UpdateOne');
        $command
            ->setParameters($request->getParsedBody())
            ->setParameter('gateSlug', $gateSlug)
            ->setParameter('userId', $user->id);

        $gate = $this->commandBus->handle($command);

        $body = [
            'data'    => $gate->toArray(),
            'updated' => $gate->updated_at
        ];

        $command = $this->commandFactory->create('ResponseDispatch');
        $command
            ->setParameter('request', $request)
            ->setParameter('response', $response)
            ->setParameter('body', $body);

        return $this->commandBus->handle($command);
    }
}
