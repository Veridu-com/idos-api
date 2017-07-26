<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Controller\Profile;

use App\Controller\ControllerInterface;
use App\Factory\Command;
use App\Repository\RepositoryInterface;
use League\Tactician\CommandBus;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Handles requests to /profiles/{userName}/attributes.
 */
class Attributes implements ControllerInterface {
    /**
     * Attribute Repository instance.
     *
     * @var \App\Repository\RepositoryInterface
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
     * @param \App\Repository\RepositoryInterface $repository
     * @param \League\Tactician\CommandBus        $commandBus
     * @param \App\Factory\Command                $commandFactory
     *
     * @return void
     */
    public function __construct(
        RepositoryInterface $repository,
        CommandBus $commandBus,
        Command $commandFactory
    ) {
        $this->repository     = $repository;
        $this->commandBus     = $commandBus;
        $this->commandFactory = $commandFactory;
    }

    /**
     * Retrieve a complete list of attributes of the given user.
     *
     * @apiEndpointParam query string names fistName,middleName,lastName
     * @apiEndpointResponse 200 schema/attribute/listAll.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @see \App\Repository\DBAttribute::getAllByUserIdAndNames
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function listAll(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $user = $request->getAttribute('targetUser');

        $entities = $this->repository->getAllByUserIdAndNames($user->id, $request->getQueryParams());

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
     * Retrieves an attribute of the profile.
     *
     * @apiEndpointResponse 200 schema/attribute/getOne.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @see \App\Repository\DBAttribute::findOneByUserIdAndName
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getOne(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $user          = $request->getAttribute('targetUser');
        $attributeName = $request->getAttribute('attributeName');

        $attribute = $this->repository->findOneByUserIdAndName($user->id, $attributeName);

        $body = [
            'data'    => $attribute->toArray(),
            'updated' => $attribute->updated_at
        ];

        $command = $this->commandFactory->create('ResponseDispatch');
        $command
            ->setParameter('request', $request)
            ->setParameter('response', $response)
            ->setParameter('body', $body);

        return $this->commandBus->handle($command);
    }
}
