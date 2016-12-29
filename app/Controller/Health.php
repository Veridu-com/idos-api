<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
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
     * NoSQL database connector instance.
     *
     * @var callable
     */
    private $noSqlConnector;
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
     * @param callable                                 $noSqlConnector
     * @param \League\Tactician\CommandBus             $commandBus
     * @param \App\Factory\Command                     $commandFactory
     *
     * @return void
     */
    public function __construct(
        \GearmanClient $gearmanClient,
        ConnectionInterface $sqlConnection,
        callable $noSqlConnector,
        CommandBus $commandBus,
        Command $commandFactory
    ) {
        $this->gearmanClient  = $gearmanClient;
        $this->sqlConnection  = $sqlConnection;
        $this->noSqlConnector = $noSqlConnector;
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
        $noSqlDatabaseStatus = false;

        try {
            $queueServerStatus = @$this->gearmanClient->ping('health-check');
        } catch (\Exception $e) {
        }

        try {
            $sqlDatabaseStatus = (bool) $this->sqlConnection->query()->get([$this->sqlConnection->raw('1')])->first();
        } catch (\Exception $e) {
        }

        try {
            $noSqlDatabaseStatus = (bool) ($this->noSqlConnector)('test', ['driver_options' => ['timeout' => 5]])
                ->getMongoDB()
                ->command(['ping' => 1])
                ->toArray()[0]
                ->ok;
        } catch (\Exception $e) {
        }

        $status = $queueServerStatus && $sqlDatabaseStatus && $noSqlDatabaseStatus;
        $body   = [
            'queue' => $queueServerStatus,
            'sql'   => $sqlDatabaseStatus,
            'nosql' => $noSqlDatabaseStatus
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
