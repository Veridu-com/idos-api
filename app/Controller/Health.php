<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Controller;

use App\Factory\Command;
use Illuminate\Database\ConnectionInterface;
use League\Tactician\CommandBus;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Handles requests to /health.
 */
class Health implements ControllerInterface {
    /**
     * System Metrics Repository instance.
     *
     * @var \GearmanClient
     */
    private $gearmanClient;
    /**
     * SQL database connection instance.
     *
     * @var \Illuminate\Database\ConnectionInterface
     */
    private $sqlConnection;
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
     * @param \GearmanClient                           $gearmanClient
     * @param \Illuminate\Database\ConnectionInterface $sqlConnection
     * @param \League\Tactician\CommandBus             $commandBus
     * @param \App\Factory\Command                     $commandFactory
     *
     * @return void
     */
    public function __construct(
        \GearmanClient $gearmanClient,
        ConnectionInterface $sqlConnection,
        CommandBus $commandBus,
        Command $commandFactory
    ) {
        $this->gearmanClient  = $gearmanClient;
        $this->sqlConnection  = $sqlConnection;
        $this->commandBus     = $commandBus;
        $this->commandFactory = $commandFactory;
    }

    /**
     * Checks the API services health status.
     *
     * @apiEndpointResponse 200 schema/health/check.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function check(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $queueServerStatus   = false;
        $sqlDatabaseStatus   = false;

        try {
            $queueServerStatus = @$this->gearmanClient->ping('health-check');
        } catch (\Exception $exception) {
        }

        try {
            $sqlDatabaseStatus = (bool) $this->sqlConnection->query()->get([$this->sqlConnection->raw('1')])->first();
        } catch (\Exception $exception) {
        }

        $status = $queueServerStatus && $sqlDatabaseStatus;
        $body   = [
            'queue' => $queueServerStatus,
            'sql'   => $sqlDatabaseStatus
        ];

        $command = $this->commandFactory->create('ResponseDispatch');
        $command
            ->setParameter('statusCode', $status === true ? 200 : 503)
            ->setParameter('request', $request)
            ->setParameter('response', $response)
            ->setParameter('body', $body);

        return $this->commandBus->handle($command);
    }

    /**
     * API internal status.
     *
     * @apiEndpointResponse 200 schema/health/status.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function status(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $body = [
            'opcache' => extension_loaded('Zend OPcache') ? opcache_get_status() : [],
            'cache'   => []
        ];
        $command = $this->commandFactory->create('ResponseDispatch');
        $command
            ->setParameter('request', $request)
            ->setParameter('response', $response)
            ->setParameter('body', $body);

        return $this->commandBus->handle($command);
    }
}
