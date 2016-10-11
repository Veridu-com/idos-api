<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Controller;

use App\Factory\Command;
use App\Repository\UserInterface;
use League\Tactician\CommandBus;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Handles requests to /profiles.
 */
class Profiles implements ControllerInterface {
    /**
     * Profile Repository instance.
     *
     * @var \App\Repository\UserInterface
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
     * @param \App\Repository\UserInterface $repository
     * @param \League\Tactician\CommandBus  $commandBus
     * @param \App\Factory\Command          $commandFactory
     *
     * @return void
     */
    public function __construct(
        UserInterface $repository,
        CommandBus $commandBus,
        Command $commandFactory
    ) {
        $this->repository     = $repository;
        $this->commandBus     = $commandBus;
        $this->commandFactory = $commandFactory;
    }

    /**
     * Lists all Profiles that are visible to the acting Company.
     *
     * @apiEndpointResponse 200 schema/profile/listAll.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function listAll(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $company = $request->getAttribute('company');

        $command = $this->commandFactory->create('Profile\\ListAll');
        $command->setParameter('company', $company);

        $entities = $this->commandBus->handle($command);

        $body = [
            'data'    => $entities->toArray(),
            'updated' => (
                $entities->isEmpty() ? time() : max($entities->max('updated_at'), $entities->max('created_at'))
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
