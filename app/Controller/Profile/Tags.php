<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Controller\Profile;

use App\Controller\ControllerInterface;
use App\Entity\User;
use App\Factory\Command;
use App\Repository\Profile\TagInterface;
use App\Repository\UserInterface;
use League\Tactician\CommandBus;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Handles requests to /companies/{companySlug}/profiles/{userId}/tags and /companies/{companySlug}/profiles/{userId}/tags/{tagSlug}.
 */
class Tags implements ControllerInterface {
    /**
     * Tag Repository instance.
     *
     * @var \App\Repository\Profile\TagInterface
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
     * @param \App\Repository\Profile\TagInterface $repository
     * @param \App\Repository\UserInterface        $userRepository
     * @param \League\Tactician\CommandBus         $commandBus
     * @param \App\Factory\Command                 $commandFactory
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
     * Lists all Tags that belongs to the Target User.
     *
     * @apiEndpointResponse 200 schema/tag/listAll.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @see \App\Repository\DBTag::getAllByUserIdAndTagSlugs
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function listAll(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $user = $this->userRepository->find($request->getAttribute('decodedUserId'));

        $tags = $this->repository->getByUserId($user->id, $request->getQueryParams());

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
     * Retrieves one Tags of the Target User based on the userName.
     *
     * @apiEndpointResponse 200 schema/tag/tagEntity.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @see \App\repository\DBTag::findOne
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getOne(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $user    = $this->userRepository->find($request->getAttribute('decodedUserId'));
        $tagSlug = $request->getAttribute('tagSlug');

        $tag = $this->repository->findOne($tagSlug, $user->id);

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
     * Creates a new Tag for the Target User.
     *
     * @apiEndpointResponse 201 schema/tag/tagEntity.json
     * @apiEndpointRequiredParam body string name Test Tag name
     * @apiEndpointParam body string slug test-tag Tag slug
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @see \App\Handler\Tag::handleCreateNew
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function createNew(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $identity = $request->getAttribute('identity');

        $user     = $this->userRepository->find($request->getAttribute('decodedUserId'));

        $command = $this->commandFactory->create('Profile\\Tag\\CreateNew');
        $command
            ->setParameters($request->getParsedBody() ?: [])
            ->setParameter('user', $user)
            ->setParameter('actor', $identity);

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
     * Deletes one Tag of the Target User based on the userId.
     *
     * @apiEndpointResponse 200 schema/tag/deleteOne.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @see \App\Handler\Tag::handleDeleteOne
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function deleteOne(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $identity = $request->getAttribute('identity');

        $user    = $this->userRepository->find($request->getAttribute('decodedUserId'));

        $command = $this->commandFactory->create('Profile\\Tag\\DeleteOne');
        $command
            ->setParameter('actor', $identity)
            ->setParameter('user', $user)
            ->setParameter('slug', $request->getAttribute('tagSlug'));

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
     * Deletes all Tags that belongs to the Target User.
     *
     * @apiEndpointResponse 200 schema/tag/deleteAll.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @see \App\Handler\Tag::handleDeleteAll
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function deleteAll(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $identity = $request->getAttribute('identity');

        $user    = $this->userRepository->find($request->getAttribute('decodedUserId'));

        $command = $this->commandFactory->create('Profile\\Tag\\DeleteAll');
        $command
            ->setParameter('actor', $identity)
            ->setParameter('user', $user);

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
