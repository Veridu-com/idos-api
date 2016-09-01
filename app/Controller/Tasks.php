<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Controller;

use App\Factory\Command;
use App\Repository\TaskInterface;
use League\Tactician\CommandBus;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Handles requests to /profiles/:userName/processes/:processId/:taskId.
 */
class Tasks implements ControllerInterface {
    /**
     * Setting Repository instance.
     *
     * @var App\Repository\TaskInterface
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
     * @param App\Repository\TaskInterface $repository
     * @param \League\Tactician\CommandBus $commandBus
     * @param App\Factory\Command          $commandFactory
     *
     * @return void
     */
    public function __construct(
        TaskInterface $repository,
        CommandBus $commandBus,
        Command $commandFactory
    ) {
        $this->repository     = $repository;
        $this->commandBus     = $commandBus;
        $this->commandFactory = $commandFactory;
    }

    /**
     * Retrieves one Task of the User.
     *
     * @apiEndpointResponse 200 schema/task/getOne.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getOne(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $taskId = $request->getAttribute('decodedTaskId');

        $task = $this->repository->find($taskId);

        $body = [
            'data'    => $task->toArray(),
            'updated' => $task->updated_at
        ];

        $command = $this->commandFactory->create('ResponseDispatch');
        $command
            ->setParameter('request', $request)
            ->setParameter('response', $response)
            ->setParameter('body', $body);

        return $this->commandBus->handle($command);
    }

    /**
     * Creates a new Task for the given process.
     *
     * @apiEndpointRequiredParam body string name XYZ Task name
     * @apiEndpointRequiredParam body string event ZYX Task event
     * @apiEndpointRequiredParam body boolean running ZYX Task running flag
     * @apiEndpointRequiredParam body boolean success ZYX Task success flag
     * @apiEndpointRequiredParam body string message ZYX Task message
     * @apiEndpointResponse 201 schema/task/createNew.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function createNew(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $processId = $request->getAttribute('decodedProcessId');

        $command = $this->commandFactory->create('Task\\CreateNew');
        $command
            ->setParameters($request->getParsedBody())
            ->setParameters(['processId' => $processId]);

        $task = $this->commandBus->handle($command);

        $body = [
            'status' => true,
            'data'   => $task->toArray()
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
     * Updates a Task.
     *
     * @apiEndpointRequiredParam body string name XYZ Task name
     * @apiEndpointRequiredParam body string event ZYX Task event
     * @apiEndpointRequiredParam body boolean running ZYX Task running flag
     * @apiEndpointRequiredParam body boolean success ZYX Task success flag
     * @apiEndpointRequiredParam body string message ZYX Task message
     * @apiEndpointResponse 201 schema/task/updateOne.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function updateOne(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $taskId = $request->getAttribute('decodedTaskId');

        $command = $this->commandFactory->create('Task\\UpdateOne');
        $command
            ->setParameters($request->getParsedBody())
            ->setParameter('id', $taskId);

        $task = $this->commandBus->handle($command);

        $body = [
            'data'    => $task->toArray(),
            'updated' => $task->updated_at
        ];

        $command = $this->commandFactory->create('ResponseDispatch');
        $command
            ->setParameter('request', $request)
            ->setParameter('response', $response)
            ->setParameter('body', $body);

        return $this->commandBus->handle($command);
    }
}
