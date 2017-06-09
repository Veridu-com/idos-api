<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Route;

use App\Controller\ControllerInterface;
use Interop\Container\ContainerInterface;
use Slim\App;

/**
 * Health check.
 *
 * This endpoint is used to ensure that the API and its services are responding properly.
 *
 * @apiDisabled
 *
 * @link docs/health/overview.md
 * @see \App\Controller\Health
 */
class Health implements RouteInterface {
    /**
     * {@inheritdoc}
     */
    public static function getPublicNames() : array {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public static function register(App $app) : void {
        $app->getContainer()[\App\Controller\Health::class] = function (ContainerInterface $container) : ControllerInterface {
            return new \App\Controller\Health(
                $container->get('gearmanClient'),
                $container->get('sql'),
                $container->get('commandBus'),
                $container->get('commandFactory')
            );
        };

        self::check($app);
        self::status($app);
    }

    /**
     * API health check.
     *
     * Checks the API health status.
     *
     * @apiEndpoint GET /health
     * @apiGroup Health
     *
     * @param \Slim\App $app
     *
     * @return void
     *
     * @link docs/health/check.md
     * @see \App\Controller\Health::check
     */
    private static function check(App $app) : void {
        $app
            ->get(
                '/health/check',
                'App\Controller\Health:check'
            )
            ->setName('health:check');
    }

    /**
     * API internal status.
     *
     * Returns the API internal status such as application opcode cache.
     *
     * @apiEndpoint GET /health/status
     * @apiGroup Health
     *
     * @param \Slim\App $app
     *
     * @return void
     *
     * @link docs/health/status.md
     * @see \App\Controller\Health::status
     */
    private static function status(App $app) : void {
        $app
            ->get(
                '/health/status',
                'App\Controller\Health:status'
            )
            ->setName('health:status');
    }
}
