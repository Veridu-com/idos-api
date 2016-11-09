<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Controller;

use App\Factory\Command;
use App\Repository\MetricInterface;
use League\Tactician\CommandBus;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Handles requests to /metrics.
 */
class Metrics implements ControllerInterface {
    /**
     * Profile Repository instance.
     *
     * @var \App\Repository\MetricInterface
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
     * @param \App\Repository\MetricInterface            $repository
     * @param \League\Tactician\CommandBus               $commandBus
     * @param \App\Factory\Command                       $commandFactory
     *
     * @return void
     */
    public function __construct(
        MetricInterface $repository,
        CommandBus $commandBus,
        Command $commandFactory
    ) {
        $this->repository          = $repository;
        $this->commandBus          = $commandBus;
        $this->commandFactory      = $commandFactory;
    }

    /**
     * Lists all Metrics.
     *
     * @apiEndpointParam query int from 1449280800 Initial metric date timestamp in number of seconds (lower bound)
     * @apiEndpointParam query int to 1449480800 Final metric date timestamp in number of seconds (upper bound)
     * @apiEndpointParam query string interval hourly|daily The interval which the measurements were made
     * @apiEndpointResponse 200 schema/metric/listAll.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function listAll(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $command = $this->commandFactory->create('Metric\\ListAll');
        $command->setParameter('queryParams', $request->getQueryParams());

        $entities = $this->commandBus->handle($command);

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
}
