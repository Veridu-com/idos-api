<?php
/**
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace App\Controller;

use App\Factory\Command;
use League\Tactician\CommandBus;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Router;

/**
 * Handles requests to /.
 */
class Main implements ControllerInterface {
    /**
     * Router instance.
     *
     * @var \Slim\Router
     */
    private $router;
    /**
     * Command Bus instance.
     *
     * @var \League\Tactician\CommandBus
     */
    private $commandBus;
    /**
     * Command Factory Instance.
     *
     * @var App\Factory\Command
     */
    private $commandFactory;

    /**
     * Class constructor.
     *
     * @param \Slim\Router                 $router
     * @param \League\Tactician\CommandBus $commandBus
     * @param App\Factory\Command          $commandFactory
     *
     * @return void
     */
    public function __construct(
        Router $router,
        CommandBus $commandBus,
        Command $commandFactory
    ) {
        $this->router         = $router;
        $this->commandBus     = $commandBus;
        $this->commandFactory = $commandFactory;
    }

    /**
     * Lists all public endpoints.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function listAll(ServerRequestInterface $request, ResponseInterface $response) {
        $classList = [
            'Companies',
            'Credentials'
        ];
        $routeList    = [];
        $publicRoutes = [];

        foreach ($this->router->getRoutes() as $route)
            $routeList[$route->getName()] = $route->getPattern();

        foreach ($classList as $className) {
            $routeClass = sprintf('\\App\\Route\\%s', $className);
            foreach ($routeClass::getPublicNames() as $routeName)
                $publicRoutes[$routeName] = $routeList[$routeName];
        }

        $command = $this->commandFactory->create('ResponseDispatch');
        $command
            ->setParameter('request', $request)
            ->setParameter('response', $response)
            ->setParameter('body', ['data' => $publicRoutes]);

        return $this->commandBus->handle($command);
    }
}
