<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Factory\Command;
use App\Repository\TagInterface;
use App\Repository\UserInterface;
use League\Tactician\CommandBus;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Handles requests to /profiles/{userName}/tags.
 */
class Tags implements ControllerInterface {
    /**
     * Tag Repository instance.
     *
     * @var App\Repository\TagInterface
     */
    private $repository;
    /**
     * User Repository instance.
     *
     * @var App\Repository\UserInterface
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
     * @var App\Factory\Command
     */
    private $commandFactory;

    /**
     * Class constructor.
     *
     * @param App\Repository\TagInterface  $repository
     * @param App\Repository\UserInterface $userRepository
     * @param \League\Tactician\CommandBus $commandBus
     * @param App\Factory\Command          $commandFactory
     *
     * @return void
     */
    public function __construct(
        TagInterface $repository,
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
     * Lists all Tags that belongs to the Target Company.
     *
     * @apiEndpointParam path string userName
     * @apiEndpointResponse 200 schema/tag/listAll.json
     *
     * @param \Psr\ServerRequestInterface $request
     * @param \Psr\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function listAll(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $targetUser = $request->getAttribute('targetUser');
        $tagNames   = $request->getQueryParam('tagName', []);

        if ($tagNames) {
            $tagNames = explode(',', $tagNames);
        }

        $tags = $this->repository->getAllByUserIdAndTagNames($targetUser->id, $tagNames);

        $body = [
            'data'    => $tags->toArray(),
            'updated' => (
                $tags->isEmpty() ? time() : max($tags->max('updatedAt'), $tags->max('createdAt'))
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
     * Creates a new Tag for the Target Company.
     *
     * @apiEndpointResponse 201 schema/tag/tagEntity.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function createNew(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $bodyRequest = $request->getParsedBody();

        $command = $this->commandFactory->create('Tag\\CreateNew');

        $command
            ->setParameters($bodyRequest)
            ->setParameter('targetUser', $request->getAttribute('targetUser'));

        $tag = $this->commandBus->handle($command);

        $body = [
            'status' => true,
            'data'   => $tag->toArray()
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
     * Retrieves one Tags of the Target Company based on the userName.
     *
     * @apiEndpointRequiredParam path string userName
     * @apiEndpointRequiredParam path string userId
     * @apiEndpointResponse 200 schema/tag/tagEntity.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getOne(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $targetUser = $request->getAttribute('targetUser');
        $tagName    = $request->getAttribute('tagName');

        $tag = $this->repository->findOneByUserIdAndName($targetUser->id, $tagName);

        $body = [
            'data' => $tag->toArray()
        ];

        $command = $this->commandFactory->create('ResponseDispatch');
        $command
            ->setParameter('request', $request)
            ->setParameter('response', $response)
            ->setParameter('body', $body);

        return $this->commandBus->handle($command);
    }

    /**
     * Deletes all Tags that belongs to the Target Company.
     *
     * @apiEndpointResponse 200 schema/tag/deleteAll.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function deleteAll(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $command = $this->commandFactory->create('Tag\\DeleteAll');
        $command->setParameter('targetUser', $request->getAttribute('targetUser'));

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

    /**
     * Deletes one Tag of the Target Company based on the userId.
     *
     * @apiEndpointRequiredParam path string userName
     * @apiEndpointRequiredParam path string userId
     * @apiEndpointResponse 200 schema/tag/deleteOne.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function deleteOne(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $command = $this->commandFactory->create('Tag\\DeleteOne');
        $command
            ->setParameter('targetUser', $request->getAttribute('targetUser'))
            ->setParameter('name', $request->getAttribute('tagName'));

        $deleted = $this->commandBus->handle($command);
        $body    = [
            'status' => $deleted === 1
        ];

        $statusCode = $body['status'] ? 200 : 404;

        $command = $this->commandFactory->create('ResponseDispatch');
        $command
            ->setParameter('statusCode', $statusCode)
            ->setParameter('request', $request)
            ->setParameter('response', $response)
            ->setParameter('body', $body);

        return $this->commandBus->handle($command);
    }
}
