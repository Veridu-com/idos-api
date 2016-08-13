<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace App\Route;

use App\Middleware\Auth;
use App\Middleware\CompanyPermission;
use Interop\Container\ContainerInterface;
use Slim\App;

/**
 * Daemon routing definitions.
 *
 * @link docs/daemons/overview.md
 * @see App\Controller\Daemons
 */
class Daemons implements RouteInterface {
    /**
     * {@inheritdoc}
     */
    public static function getPublicNames() : array {
        return [
            'daemons:listAll'
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function register(App $app) {
        $app->getContainer()[\App\Controller\Daemons::class] = function (ContainerInterface $container) {
            return new \App\Controller\Daemons(
                $container->get('repositoryFactory')->create('Daemon'),
                $container->get('commandBus'),
                $container->get('commandFactory'),
                $container->get('optimus')
            );
        };

        $container              = $app->getContainer();
        $authMiddleware         = $container->get('authMiddleware');
        $companyPermissionMiddleware   = $container->get('companyPermissionMiddleware');

        self::listAll($app, $authMiddleware, $companyPermissionMiddleware);
    }

    /**
     * List all Daemons.
     *
     * Retrieve a complete list of daemons.
     *
     * @apiEndpoint GET /daemons
     * @apiGroup Company Daemons
     * @apiAuth header key compPrivKey 2f476be4f457ef606f3b9177b5bf19c9 Company's Private Key
     * @apiAuth query key compPrivKey 2f476be4f457ef606f3b9177b5bf19c9 Company's Private Key
     *
     * @param \Slim\App $app
     * @param \callable $auth
     *
     * @return void
     *
     * @link docs/daemons/listAll.md
     * @see App\Middleware\Auth::__invoke
     * @see App\Middleware\CompanyPermission::__invoke
     * @see App\Controller\Daemons::listAll
     */
    private static function listAll(App $app, callable $auth, callable $permission) {
        $app
            ->get(
                '/daemons',
                'App\Controller\Daemons:listAll'
            )
            ->add($permission(CompanyPermission::PRIVATE_ACTION))
            ->add($auth(Auth::COMP_PRIVKEY))
            ->setName('daemons:listAll');
    }
}
