<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Controller;

use App\Factory\Command;
use League\Tactician\CommandBus;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Handles requests to /companies and /companies/{companySlug}.
 */
class Companies implements ControllerInterface {
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
     * @param \League\Tactician\CommandBus $commandBus
     * @param \App\Factory\Command         $commandFactory
     *
     * @return void
     */
    public function __construct(
        CommandBus $commandBus,
        Command $commandFactory
    ) {
        $this->commandBus     = $commandBus;
        $this->commandFactory = $commandFactory;
    }

    /**
     * List all child Companies that belongs to the Acting Company.
     *
     * @apiEndpointParam query string after 2016-01-01|1070-01-01 Initial Company creation date (lower bound)
     * @apiEndpointParam query string before 2016-01-31|2016-12-31 Final Company creation date (upper bound)
     * @apiEndpointParam query int page 10|1 Current page
     * @apiEndpointResponse 200 schema/company/listAll.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function listAll(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $identity  = $request->getAttribute('identity');
        $companies = $identity->company();

        $body = [
            'data'    => $companies->toArray(),
            'updated' => (
                $companies->isEmpty() ? time() : max($companies->max('updatedAt'), $companies->max('createdAt'))
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
     * Retrieves the Target Company, a child of the Acting Company.
     *
     * @apiEndpointResponse 200 schema/company/getOne.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getOne(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $targetCompany = $request->getAttribute('targetCompany');

        $body = [
            'data'    => $targetCompany->toArray(),
            'updated' => $targetCompany->updatedAt
        ];

        $command = $this->commandFactory->create('ResponseDispatch');
        $command
            ->setParameter('request', $request)
            ->setParameter('response', $response)
            ->setParameter('body', $body);

        return $this->commandBus->handle($command);
    }

    /**
     * Creates a new child Company for the Acting Company.
     *
     * @apiEndpointRequiredParam body string name NewCo. Company name
     * @apiEndpointResponse 201 schema/company/createNew.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @see \App\Handler\Company::handleCreateNew
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function createNew(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $company  = $request->getAttribute('targetCompany');
        $identity = $request->getAttribute('identity');

        $command = $this->commandFactory->create('Company\CreateNew');
        $command
            ->setParameters($request->getParsedBody() ?: [])
            ->setParameter('identity', $identity)
            ->setParameter('parentId', $company->id);
        $company = $this->commandBus->handle($command);

        $body = [
            'data' => $company->toArray()
        ];

        $command = $this->commandFactory->create('ResponseDispatch');
        $command
            ->setParameter('request', $request)
            ->setParameter('response', $response)
            ->setParameter('body', $body)
            ->setParameter('statusCode', 201);

        return $this->commandBus->handle($command);
    }

    /**
     * Updates the Target Company, a child of the Acting Company.
     *
     * @apiEndpointRequiredParam body string name NewName New Company name
     * @apiEndpointResponse 200 schema/company/updateOne.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @see \App\Handler\Company::handleUpdateOne
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function updateOne(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $targetCompany = $request->getAttribute('targetCompany');
        $identity      = $request->getAttribute('identity');

        $command = $this->commandFactory->create('Company\UpdateOne');
        $command
            ->setParameters($request->getParsedBody() ?: [])
            ->setParameter('identity', $identity)
            ->setParameter('company', $targetCompany);
        $targetCompany = $this->commandBus->handle($command);

        $body = [
            'data'    => $targetCompany->toArray(),
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
     * Deletes the Target Company, a child of the Acting Company.
     *
     * @apiEndpointResponse 200 schema/company/deleteOne.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @see \App\Handler\Company::handleDeleteOne
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function deleteOne(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $targetCompany = $request->getAttribute('targetCompany');
        $identity      = $request->getAttribute('identity');

        $command = $this->commandFactory->create('Company\DeleteOne');
        $command->setParameter('company', $targetCompany);
        $command->setParameter('identity', $identity);

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
