<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Controller\Company;

use App\Controller\ControllerInterface;
use App\Factory\Command;
use App\Repository\Company\CredentialInterface;
use App\Repository\Company\SubscriptionInterface;
use League\Tactician\CommandBus;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Handles requests to /management/credentials and /management/credentials/{pubKey}.
 */
class Credentials implements ControllerInterface {
    /**
     * Credential Repository instance.
     *
     * @var \App\Repository\Company\CredentialInterface
     */
    private $repository;
    /**
     * Credential Repository instance.
     *
     * @var \App\Repository\Company\SubscriptionInterface
     */
    private $subscriptionRepository;
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
     * @param \App\Repository\Company\CredentialInterface $repository
     * @param \League\Tactician\CommandBus               $commandBus
     * @param \App\Factory\Command                        $commandFactory
     *
     * @return void
     */
    public function __construct(
        CredentialInterface $repository,
        SubscriptionInterface $subscriptionRepository,
        CommandBus $commandBus,
        Command $commandFactory
    ) {
        $this->repository                 = $repository;
        $this->subscriptionRepository     = $subscriptionRepository;
        $this->commandBus                 = $commandBus;
        $this->commandFactory             = $commandFactory;
    }

    /**
     * Lists all Credentials that belongs to the Target Company.
     *
     * @apiEndpointResponse 200 schema/credential/listAll.json
     *
     * @param \Psr\ServerRequestInterface $request
     * @param \Psr\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function listAll(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $company = $request->getAttribute('targetCompany');

        $credentials = $this->repository->getByCompanyId($company->id);

        $body = [
            'status'  => true,
            'data'    => $credentials->toArray(),
            'updated' => (
                $credentials->isEmpty() ? time() : max($credentials->max('updatedAt'), $credentials->max('createdAt'))
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
     * Retrieves one Credential of the Target Company based on the Credential's Public Key.
     *
     * @apiEndpointResponse 200 schema/credential/getOne.json
     *
     * @param \Psr\ServerRequestInterface $request
     * @param \Psr\ResponseInterface      $response
     *
     * @see \App\Repository\DBCredential::findByPubKey
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getOne(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $company = $request->getAttribute('targetCompany');

        $credential = $this->repository->findByPubKey($request->getAttribute('pubKey'));

        $body = [
            'data'    => $credential->toArray(),
            'updated' => $credential->updated_at
        ];

        $command = $this->commandFactory->create('ResponseDispatch');
        $command
            ->setParameter('request', $request)
            ->setParameter('response', $response)
            ->setParameter('body', $body);

        return $this->commandBus->handle($command);
    }

    /**
     * Creates a new Credential for the Target Company.
     *
     * @apiEndpointRequiredParam body string name My-Credential Credential name
     * @apiEndpointRequiredParam body bool production false Production flag
     * @apiEndpointResponse 201 schema/credential/createNew.json
     *
     * @param \Psr\ServerRequestInterface $request
     * @param \Psr\ResponseInterface      $response
     *
     * @see \App\Handler\Credential::handleCreateNew
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function createNew(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $company  = $request->getAttribute('targetCompany');
        $identity = $request->getAttribute('identity');

        $command = $this->commandFactory->create('Company\\Credential\\CreateNew');
        $command
            ->setParameters($request->getParsedBody() ?: [])
            ->setParameter('identity', $identity)
            ->setParameter('company', $company);

        $credential = $this->commandBus->handle($command);

        $body = [
            'data' => $credential->toArray()
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
     * Updates one Credential of the Target Company based on the Credential's Public Key.
     *
     * @apiEndpointRequiredParam body string name New-Name New Credential name
     * @apiEndpointResponse 200 schema/credential/updateOne.json
     *
     * @param \Psr\ServerRequestInterface $request
     * @param \Psr\ResponseInterface      $response
     *
     * @see \App\Repository\DBCredential::findByPubKey
     * @see \App\Handler\Credential::handleUpdateOne
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function updateOne(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $identity   = $request->getAttribute('identity');
        $credential = $this->repository->findByPubKey($request->getAttribute('pubKey'));

        $command = $this->commandFactory->create('Company\\Credential\\UpdateOne');
        $command
            ->setParameters($request->getParsedBody() ?: [])
            ->setParameter('identity', $identity)
            ->setParameter('credentialId', $credential->id);

        $credential = $this->commandBus->handle($command);

        $body = [
            'data'    => $credential->toArray(),
            'updated' => $credential->updatedAt
        ];

        $command = $this->commandFactory->create('ResponseDispatch');
        $command
            ->setParameter('request', $request)
            ->setParameter('response', $response)
            ->setParameter('body', $body);

        return $this->commandBus->handle($command);
    }

    /**
     * Deletes one Credential of the Target Company based on the Credential's Public Key.
     *
     * @apiEndpointResponse 200 schema/credential/deleteOne.json
     *
     * @param \Psr\ServerRequestInterface $request
     * @param \Psr\ResponseInterface      $response
     *
     * @see \App\Repository\DBCredential::findByPubKey
     * @see \App\Handler\Credential::handleDeleteOne
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function deleteOne(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $identity   = $request->getAttribute('identity');
        $credential = $this->repository->findByPubKey($request->getAttribute('pubKey'));

        $command = $this->commandFactory->create('Company\\Credential\\DeleteOne');
        $command
            ->setParameter('credential', $credential)
            ->setParameter('identity', $identity);

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
