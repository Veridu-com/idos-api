<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Controller\Company;

use App\Controller\ControllerInterface;
use App\Factory\Command;
use App\Repository\RepositoryInterface;
use League\Tactician\CommandBus;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Handles requests to /companies/{companySlug}/members and /companies/{companySlug}/members/{memberId}.
 */
class Members implements ControllerInterface {
    /**
     * Member Repository instance.
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
        $this->repository           = $repository;
        $this->commandBus           = $commandBus;
        $this->commandFactory       = $commandFactory;
    }

    /**
     * Lists all Members that belongs to the Target Company.
     *
     * @apiEndpointResponse 200 schema/member/listAll.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function listAll(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $company = $request->getAttribute('targetCompany');
        $members = $this->repository->getByCompanyId($company->id, $request->getQueryParams());

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
     * Retrieve the membership of the related company and identity.
     *
     * @apiEndpointResponse 200 schema/member/memberEntity.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @see \App\Repository\DBMember::findOne
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getOne(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $member = $this->repository->find($request->getAttribute('decodedMemberId'));

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
     * Updates a company member.
     *
     * Updates a company member identified by the given id.
     *
     * @apiEndpointRequiredParam body string role company.owner Role type
     * @apiEndpointResponse 200 schema/member/updateOne.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @see \App\Handler\Member::handleUpdateOne
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function updateOne(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $targetCompany = $request->getAttribute('targetCompany');
        $identity      = $request->getAttribute('identity');
        $memberId      = $request->getAttribute('decodedMemberId');

        $command = $this->commandFactory->create('Company\Member\UpdateOne');
        $command
            ->setParameter('company', $targetCompany)
            ->setParameter('identity', $identity)
            ->setParameter('memberId', $memberId)
            ->setParameter('ipaddr', $request->getAttribute('ip_address'))
            ->setParameters($request->getParsedBody());

        $member = $this->commandBus->handle($command);

        $body = [
            'data'    => $member->toArray(),
            'updated' => time()
        ];

        $command = $this->commandFactory->create('ResponseDispatch');
        $command
            ->setParameter('request', $request)
            ->setParameter('response', $response)
            ->setParameter('body', $body);

        return $this->commandBus->handle($command);
    }

    /**
     * Deletes a Company member.
     *
     * @apiEndpointResponse 200 schema/member/deleteOne.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @see \App\Handler\Member::handleDeleteOne
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function deleteOne(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $targetCompany = $request->getAttribute('targetCompany');
        $identity      = $request->getAttribute('identity');
        $memberId      = $request->getAttribute('decodedMemberId');

        $command = $this->commandFactory->create('Company\Member\DeleteOne');
        $command
            ->setParameter('company', $targetCompany)
            ->setParameter('identity', $identity)
            ->setParameter('memberId', $memberId)
            ->setParameter('ipaddr', $request->getAttribute('ip_address'));

        $success = $this->commandBus->handle($command);

        $body = [
            'status' => (bool) $success,
        ];

        $command = $this->commandFactory->create('ResponseDispatch');
        $command
            ->setParameter('request', $request)
            ->setParameter('response', $response)
            ->setParameter('body', $body);

        return $this->commandBus->handle($command);
    }

    /**
     * Gets the membership of the requesting identity.
     *
     * @apiEndpointRequiredParam body string role company.owner Role type
     * @apiEndpointRequiredParam body string email jhondoe@idos.io User's email
     * @apiEndpointResponse 200 schema/member/getMembership.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getMembership(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $company  = $request->getAttribute('targetCompany');
        $identity = $request->getAttribute('identity');

        $member = $this->repository->findMembership($identity->id, $company->id);

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
}
