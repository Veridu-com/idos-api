<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Controller\Profile;

use App\Controller\ControllerInterface;
use App\Factory\Command;
use App\Repository\Profile\ProcessInterface;
use App\Repository\Profile\TaskInterface;
use League\Tactician\CommandBus;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Handles requests to /profiles/{userName}/processes/{processId}/tasks and /profiles/{userName}/processes/{processId}/tasks/{taskId}.
 */
class Tasks implements ControllerInterface {
    /**
     * Setting Repository instance.
     *
     * @var \App\Repository\Profile\TaskInterface
     */
    private $repository;
    /**
     * Setting Repository instance.
     *
     * @var \App\Repository\Profile\ProcessInterface
     */
    private $processRepository;
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
     * @param \App\Repository\Profile\TaskInterface    $repository
     * @param \App\Repository\Profile\ProcessInterface $processRepository
     * @param \League\Tactician\CommandBus             $commandBus
     * @param \App\Factory\Command                     $commandFactory
     *
     * @return void
     */
    public function __construct(
        TaskInterface $repository,
        ProcessInterface $processRepository,
        CommandBus $commandBus,
        Command $commandFactory
    ) {
        $this->repository        = $repository;
        $this->processRepository = $processRepository;
        $this->commandBus        = $commandBus;
        $this->commandFactory    = $commandFactory;
    }

    /**
     * Retrieves one Task of the User.
     *
     * @apiEndpointResponse 200 schema/task/getOne.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @see \App\Repository\Profile\DBTask::find
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getOne(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $taskId = $request->getAttribute('decodedTaskId');

        $task = $this->repository->find($taskId);

        $body = [
            'data'    => $task->toArray(),
            'updated' => $task->updatedAt
        ];

        $command = $this->commandFactory->create('ResponseDispatch');
        $command
            ->setParameter('request', $request)
            ->setParameter('response', $response)
            ->setParameter('body', $body);

        return $this->commandBus->handle($command);
    }

    /**
     * Lists all Tasks that belongs to the given process.
     *
     * @apiEndpointParam query int page 10|1 Current page
     * @apiEndpointResponse 200 schema/task/listAll.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @see \App\Repository\DBProcess::find
     * @see \App\Repository\Profile\DBTask::getAllByProcessId
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function listAll(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $processId = $request->getAttribute('decodedProcessId');

        $process = $this->processRepository->find($processId);
        $result  = $this->repository->getAllByProcessId($process->id, $request->getQueryParams());

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
     * Creates a new Task for the given process.
     *
     * @apiEndpointRequiredParam body string name Task test Task name
     * @apiEndpointRequiredParam body string event user:created Task event
     * @apiEndpointParam body boolean running false Task running flag
     * @apiEndpointParam body boolean success true Task success flag
     * @apiEndpointParam body string message xyz Task message
     * @apiEndpointResponse 201 schema/task/createNew.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @see \App\Handler\Profile\Task::handleCreateNew
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function createNew(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $credential = $request->getAttribute('credential');
        $processId  = $request->getAttribute('decodedProcessId');

        $command = $this->commandFactory->create('Profile\\Task\\CreateNew');
        $command
            ->setParameters($request->getParsedBody() ?: [])
            ->setParameter('credential', $credential)
            ->setParameter('service', $request->getAttribute('service'))
            ->setParameter('processId', $processId);

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
     * @apiEndpointParam body boolean running false Task running flag
     * @apiEndpointParam body boolean success true Task success flag
     * @apiEndpointParam body string message xyz Task message
     * @apiEndpointResponse 200 schema/task/updateOne.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @see \App\Handler\Profile\Task::handleUpdateOne
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function updateOne(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $taskId     = $request->getAttribute('decodedTaskId');
        $user       = $request->getAttribute('targetUser');
        $credential = $request->getAttribute('credential');

        $command = $this->commandFactory->create('Profile\\Task\\UpdateOne');
        $command
            ->setParameters($request->getParsedBody() ?: [])
            ->setParameter('user', $user)
            ->setParameter('credential', $credential)
            ->setParameter('id', $taskId);

        $task = $this->commandBus->handle($command);

        $body = [
            'data'    => $task->toArray(),
            'updated' => $task->updatedAt
        ];

        $command = $this->commandFactory->create('ResponseDispatch');
        $command
            ->setParameter('request', $request)
            ->setParameter('response', $response)
            ->setParameter('body', $body);

        return $this->commandBus->handle($command);
    }
}
