<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Controller;

use App\Factory\Command;
use App\Repository\Metric\SystemInterface;
use App\Repository\Metric\UserInterface;
use League\Tactician\CommandBus;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Handles requests to /metrics.
 */
class Metrics implements ControllerInterface {
    /**
     * System Metrics Repository instance.
     *
     * @var \App\Repository\Metric\SystemInterface
     */
    private $systemMetricsRepository;
    /**
     * User Metrics Repository instance.
     *
     * @var \App\Repository\Metric\UserInterface
     */
    private $userMetricsRepository;
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
     * @param \App\Repository\Metric\SystemInterface $systemMetricsRepository
     * @param \App\Repository\Metric\UserInterface   $userMetricsRepository
     * @param \League\Tactician\CommandBus           $commandBus
     * @param \App\Factory\Command                   $commandFactory
     *
     * @return void
     */
    public function __construct(
        SystemInterface $systemMetricsRepository,
        UserInterface $userMetricsRepository,
        CommandBus $commandBus,
        Command $commandFactory
    ) {
        $this->systemMetricsRepository  = $systemMetricsRepository;
        $this->userMetricsRepository    = $userMetricsRepository;
        $this->commandBus               = $commandBus;
        $this->commandFactory           = $commandFactory;
    }

    /**
     * Lists all system metrics.
     *
     * @apiEndpointParam query int from 1449280800 Initial metric date timestamp in number of seconds (lower bound)
     * @apiEndpointParam query int to 1449480800 Final metric date timestamp in number of seconds (upper bound)
     * @apiEndpointParam query string interval hourly|daily The interval which the measurements were made
     * @apiEndpointResponse 200 schema/metric/listAllSystem.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function listAllSystem(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $targetCompany = $request->getAttribute('targetCompany');

        $command = $this->commandFactory->create('Metric\ListAllSystem');
        $command
            ->setParameter('queryParams', $request->getQueryParams())
            ->setParameter('targetCompany', $targetCompany);

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

    /**
     * Lists all user metrics.
     *
     * @apiEndpointParam query int from 1449280800 Initial metric date timestamp in number of seconds (lower bound)
     * @apiEndpointParam query int to 1449480800 Final metric date timestamp in number of seconds (upper bound)
     * @apiEndpointParam query string interval hourly|daily The interval which the measurements were made
     * @apiEndpointResponse 200 schema/metric/listAllUser.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function listAllUser(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $targetCompany = $request->getAttribute('targetCompany');

        $command = $this->commandFactory->create('Metric\ListAllUser');
        $command
            ->setParameter('queryParams', $request->getQueryParams())
            ->setParameter('targetCompany', $targetCompany);

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
