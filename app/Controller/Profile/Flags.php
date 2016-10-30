<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Controller\Profile;

use App\Controller\ControllerInterface;
use App\Factory\Command;
use App\Repository\Profile\FlagInterface;
use App\Repository\UserInterface;
use League\Tactician\CommandBus;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Handles requests to /profiles/{userName}/flags and /profiles/{userName}/flags/{flagSlug}.
 */
class Flags implements ControllerInterface {
    /**
     * Flag Repository instance.
     *
     * @var \App\Repository\Profile\FlagInterface
     */
    private $repository;
    /**
     * User Repository instance.
     *
     * @var \App\Repository\UserInterface
     */
    private $userRepository;
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
     * @param \App\Repository\Profile\FlagInterface $repository
     * @param \App\Repository\UserInterface         $userRepository
     * @param \League\Tactician\CommandBus          $commandBus
     * @param \App\Factory\Command                  $commandFactory
     *
     * @return void
     */
    public function __construct(
        FlagInterface $repository,
        UserInterface $userRepository,
        CommandBus $commandBus,
        Command $commandFactory
    ) {
        $this->repository     = $repository;
        $this->userRepository = $userRepository;
        $this->commandBus     = $commandBus;
        $this->commandFactory = $commandFactory;
    }

    /**
     * Retrieves a list of flags that belongs to the user.
     *
     * @apiEndpointParam query int page 10|1 Current page
     * @apiEndpointResponse 200 schema/flag/listAll.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @see \App\Repository\DBFlag::findBy
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function listAll(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $user = $request->getAttribute('targetUser');

        $entities = $this->repository->getByUserId($user->id, $request->getQueryParams());

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
     * Retrieves a flag from the user.
     *
     * @apiEndpointResponse 200 schema/flag/getOne.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @see \App\Repository\DBFlag::findOne
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getOne(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $user    = $request->getAttribute('targetUser');
        $service = $request->getAttribute('service');
        $slug    = $request->getAttribute('flagSlug');

        $entity = $this->repository->findOne($slug, $service->id, $user->id);

        $body = [
            'data'    => $entity->toArray(),
            'updated' => $entity->updated_at
        ];

        $command = $this->commandFactory->create('ResponseDispatch');
        $command
            ->setParameter('request', $request)
            ->setParameter('response', $response)
            ->setParameter('body', $body);

        return $this->commandBus->handle($command);
    }

    /**
     * Creates a new flag for the user.
     *
     * @apiEndpointRequiredParam body string slug flag middle-name-mismatch Flag slug
     * @apiEndpointRequiredParam body string attribute middle-name Flag attribute
     * @apiEndpointResponse 201 schema/flag/createNew.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @see \App\Handler\Flag::handleCreateNew
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function createNew(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $user    = $request->getAttribute('targetUser');
        $service = $request->getAttribute('service');
        $credential = $request->getAttribute('credential');

        $command = $this->commandFactory->create('Profile\\Flag\\CreateNew');
        $command
            ->setParameters($request->getParsedBody() ?: [])
            ->setParameter('actor', $credential)
            ->setParameter('user', $user)
            ->setParameter('service', $service);

        $flag = $this->commandBus->handle($command);

        $body = [
            'status' => true,
            'data'   => $flag->toArray()
        ];

        $command = $this->commandFactory->create('ResponseDispatch');
        $command
            ->setParameter('statusCode', 201)
            ->setParameter('request', $request)
            ->setParameter('response', $response)
            ->setParameter('body', $body);

        return $this->commandBus->handle($command);
    }

    /**
     * Deletes one Flag of the User.
     *
     * @apiEndpointResponse 200 schema/flag/deleteOne.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @see \App\Handler\Flag::handleDeleteOne
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function deleteOne(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $user    = $request->getAttribute('targetUser');
        $service = $request->getAttribute('service');
        $slug    = $request->getAttribute('flagSlug');
        $credential = $request->getAttribute('credential');

        $command = $this->commandFactory->create('Profile\\Flag\\DeleteOne');
        $command
            ->setParameter('actor', $credential)
            ->setParameter('user', $user)
            ->setParameter('service', $service)
            ->setParameter('slug', $slug);

        $this->commandBus->handle($command);
        $body = [
            'status' => true
        ];

        $command = $this->commandFactory->create('ResponseDispatch');
        $command
            ->setParameter('request', $request)
            ->setParameter('response', $response)
            ->setParameter('body', $body);

        return $this->commandBus->handle($command);
    }

    /**
     * Deletes all Flags that belongs to the User.
     *
     * @apiEndpointResponse 200 schema/flag/deleteAll.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @see \App\Handler\Flag::handleDeleteAll
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function deleteAll(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $user    = $request->getAttribute('targetUser');
        $service = $request->getAttribute('service');
        $credential = $request->getAttribute('credential');

        $command = $this->commandFactory->create('Profile\\Flag\\DeleteAll');
        $command
            ->setParameter('actor', $credential)
            ->setParameter('user', $user)
            ->setParameter('service', $service)
            ->setParameter('queryParams', $request->getQueryParams());

        $body = [
            'deleted' => $this->commandBus->handle($command)
        ];

        $command = $this->commandFactory->create('ResponseDispatch');
        $command
            ->setParameter('request', $request)
            ->setParameter('response', $response)
            ->setParameter('body', $body);

        return $this->commandBus->handle($command);
    }
}
