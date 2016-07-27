<?php

declare(strict_types=1);
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace App\Controller;

use App\Factory\Command;
use App\Repository\CredentialInterface;
use League\Tactician\CommandBus;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Handles requests to /companies/{companySlug}/credentials and /companies/{companySlug}/credentials/{pubKey}.
 */
class Credentials implements ControllerInterface {
    /**
     * Credential Repository instance.
     *
     * @var App\Repository\CredentialInterface
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
     * @var App\Factory\Command
     */
    private $commandFactory;

    /**
     * Class constructor.
     *
     * @param App\Repository\CredentialInterface $repository
     * @param \League\Tactician\CommandBus       $commandBus
     * @param App\Factory\Command                $commandFactory
     *
     * @return void
     */
    public function __construct(
        CredentialInterface $repository,
        CommandBus $commandBus,
        Command $commandFactory
    ) {
        $this->repository     = $repository;
        $this->commandBus     = $commandBus;
        $this->commandFactory = $commandFactory;
    }

    /**
     * Lists all Credentials that belongs to the Target Company.
     *
     * @apiEndpointParam query string after 2016-01-01|1070-01-01 Initial Credential creation date (lower bound)
     * @apiEndpointParam query string before 2016-01-31|2016-12-31 Final Credential creation date (upper bound)
     * @apiEndpointParam query int page 10|1 Current page
     * @apiEndpointResponse 200 schema/credential/listAll.json
     *
     * @param \Psr\ServerRequestInterface $request
     * @param \Psr\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function listAll(ServerRequestInterface $request, ResponseInterface $response) {
        $targetCompany = $request->getAttribute('targetCompany');

        $credentials = $this->repository->getAllByCompanyId($targetCompany->id);

        $body = [
            'status'  => true,
            'data'    => $credentials->toArray(),
            'updated' => (
                $credentials->isEmpty() ? time() : $credentials->max('updated_at')
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
     * Creates a new Credential for the Target Company.
     *
     * @apiEndpointRequiredParam body string name My-Credential Credential name
     * @apiEndpointRequiredParam body bool production false Production flag
     * @apiEndpointResponse 201 schema/credential/createNew.json
     *
     * @param \Psr\ServerRequestInterface $request
     * @param \Psr\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function createNew(ServerRequestInterface $request, ResponseInterface $response) {
        $targetCompany = $request->getAttribute('targetCompany');

        $command = $this->commandFactory->create('Credential\\CreateNew');
        $command
            ->setParameters($request->getParsedBody())
            ->setParameter('companyId', $targetCompany->id);

        $credential = $this->commandBus->handle($command);

        $body = [
            'data'   => $credential->toArray()
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
     * Deletes all Credentials that belongs to the Target Company.
     *
     * @apiEndpointResponse 200 schema/credential/deleteAll.json
     *
     * @param \Psr\ServerRequestInterface $request
     * @param \Psr\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function deleteAll(ServerRequestInterface $request, ResponseInterface $response) {
        $targetCompany = $request->getAttribute('targetCompany');

        $command = $this->commandFactory->create('Credential\\DeleteAll', [$targetCompany->id]);
        $deleted = $this->commandBus->handle($command);

        $body = [
            'deleted' => $deleted
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
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getOne(ServerRequestInterface $request, ResponseInterface $response) {
        $targetCompany = $request->getAttribute('targetCompany');

        $credential = $this->repository->findByPubKey($request->getAttribute('pubKey'), $targetCompany->id);

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
     * Updates one Credential of the Target Company based on the Credential's Public Key.
     *
     * @apiEndpointRequiredParam body string name New-Name New Credential name
     * @apiEndpointResponse 200 schema/credential/updateOne.json
     *
     * @param \Psr\ServerRequestInterface $request
     * @param \Psr\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function updateOne(ServerRequestInterface $request, ResponseInterface $response) {
        $targetCompany = $request->getAttribute('targetCompany');
        $credential    = $this->repository->findByPubKey($request->getAttribute('pubKey'));

        $command = $this->commandFactory->create('Credential\\UpdateOne');
        $command
            ->setParameters($request->getParsedBody())
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
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function deleteOne(ServerRequestInterface $request, ResponseInterface $response) {
        $targetCompany = $request->getAttribute('targetCompany');

        $credential = $this->repository->findByPubKey($request->getAttribute('pubKey'), $targetCompany->id);

        $command = $this->commandFactory->create('Credential\\DeleteOne');
        $command
            ->setParameter('credentialId', $credential->id);

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
