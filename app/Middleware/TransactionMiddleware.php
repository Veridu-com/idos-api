<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Middleware;

use Illuminate\Database\ConnectionInterface;
use League\Tactician\Middleware;

class TransactionMiddleware implements Middleware {
    /**
     * DB Connection.
     *
     * @var \Illuminate\Database\ConnectionInterface
     */
    protected $dbConnection;

    /**
     * Class constructor.
     *
     * @param ConnectionInterface $dbConnection
     *
     * @return void
     */
    public function __construct(ConnectionInterface $dbConnection) {
        $this->dbConnection = $dbConnection;
    }

    /**
     * Executes commands inside a transaction.
     *
     * @param mixed    $command
     * @param callable $next
     *
     * @return mixed
     */
    public function execute($command, callable $next) {
        try {
            $this->dbConnection->beginTransaction();
            $returnValue = $next($command);
            $this->dbConnection->commit();

            return $returnValue;
        } catch (\Exception $exception) {
            $this->dbConnection->rollBack();
            throw $exception;
        }
    }
}
