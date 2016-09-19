<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Controller;

use App\Entity\User;
use App\Factory\Command;
use App\Repository\MemberInterface;
use App\Repository\UserInterface;
use League\Tactician\CommandBus;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Handles requests to /management/members.
 */
class Members implements ControllerInterface {
    /**
     * Member Repository instance.
     *
     * @var App\Repository\MemberInterface
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
     * @param App\Repository\MemberInterface $repository
     * @param App\Repository\UserInterface   $userRepository
     * @param \League\Tactician\CommandBus   $commandBus
     * @param App\Factory\Command            $commandFactory
     *
     * @return void
     */
    public function __construct(
        MemberInterface $repository,
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
     * Lists all Members that belongs to the Target Company.
     *
     * @apiEndpointResponse 200 schema/member/listAll.json
     *
     * @param \Psr\ServerRequestInterface $request
     * @param \Psr\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function listAll(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $company = $request->getAttribute('targetCompany');
        $members = $this->repository->getAllByCompanyId($company->id, $request->getQueryParams());

        $body = [
            'data'    => $members->toArray(),
            'updated' => (
                $members->isEmpty() ? time() : max($members->max('updatedAt'), $members->max('createdAt'))
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
     * Creates a new Member for the Target Company.
     *
     * @apiEndpointRequiredParam body string role company:owner Role type
     * @apiEndpointRequiredParam body string userName jhondoe UserName
     * @apiEndpointResponse 201 schema/member/memberEntity.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @see App\Handler\Member::handleCreateNew
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function createNew(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $bodyRequest = $request->getParsedBody();

        $command = $this->commandFactory->create('Member\\CreateNew');

        $command
            ->setParameters($bodyRequest);

        $member = $this->commandBus->handle($command);

        $body = [
            'status' => true,
            'data'   => $member->toArray()
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
     * Updates one Member of the Target Company based on the userId.
     *
     * @apiEndpointRequiredParam body string role company:owner
     * @apiEndpointResponse 200 schema/member/updateOne.json
     *
     * @param \Psr\ServerRequestInterface $request
     * @param \Psr\ResponseInterface      $response
     *
     * @see App\Handler\Handler::handleUpdateOne
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function updateOne(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $command = $this->commandFactory->create('Member\\UpdateOne');
        $command
            ->setParameter('memberId', $request->getAttribute('decodedMemberId'))
            ->setParameters($request->getParsedBody());

        $member = $this->commandBus->handle($command);

        $body = [
            'data'    => $member->toArray(),
            'updated' => $member->updated_at
        ];

        $command = $this->commandFactory->create('ResponseDispatch');
        $command
            ->setParameter('request', $request)
            ->setParameter('response', $response)
            ->setParameter('body', $body);

        return $this->commandBus->handle($command);
    }

    /**
     * Retrieves one Member of the Target Company based on the userName.
     *
     * @apiEndpointResponse 200 schema/member/memberEntity.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @see App\Repository\DBMember::findOne
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getOne(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $member = $this->repository->findOne($request->getAttribute('decodedMemberId'));

        $body = [
            'data' => $member->toArray()
        ];

        $command = $this->commandFactory->create('ResponseDispatch');
        $command
            ->setParameter('request', $request)
            ->setParameter('response', $response)
            ->setParameter('body', $body);

        return $this->commandBus->handle($command);
    }

    /**
     * Deletes all Members that belongs to the Target Company.
     *
     * @apiEndpointResponse 200 schema/member/deleteAll.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @see App\Handler\Member:handleDeleteAll
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function deleteAll(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $company = $request->getAttribute('company');
        $command = $this->commandFactory->create('Member\\DeleteAll');

        $command->setParameter('companyId', $company->id);

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
     * Deletes one Member of the Target Company based on the userId.
     *
     * @apiEndpointResponse 200 schema/member/deleteOne.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @see App\Handler\Member:handleDeleteOne
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function deleteOne(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $command = $this->commandFactory->create('Member\\DeleteOne');
        $command->setParameter('memberId', $request->getAttribute('decodedMemberId'));

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
}
