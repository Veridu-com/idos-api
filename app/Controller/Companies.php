<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Controller;

use App\Factory\Command;
use App\Repository\CompanyInterface;
use Jenssegers\Optimus\Optimus;
use League\Tactician\CommandBus;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Handles requests to /companies and /companies/{companySlug}.
 * A Company is a profile within the API available for a customer who requires
 * a sophisticated level of control over employees with different levels of Permission.
 */
class Companies implements ControllerInterface {
    /**
     * Company Repository instance.
     *
     * @var App\Repository\CompanyInterface
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
     * Optimus instance.
     *
     * @var \Jenssegers\Optimus\Optimus
     */
    private $optimus;

    /**
     * Class constructor.
     *
     * @param App\Repository\CompanyInterface $repository
     * @param \League\Tactician\CommandBus    $commandBus
     * @param App\Factory\Command             $commandFactory
     *
     * @return void
     */
    public function __construct(
        CompanyInterface $repository,
        CommandBus $commandBus,
        Command $commandFactory,
        Optimus $optimus
    ) {
        $this->repository     = $repository;
        $this->commandBus     = $commandBus;
        $this->commandFactory = $commandFactory;
        $this->optimus        = $optimus;
    }

    /**
     * Retrieves a complete list of all child companies that belong to the requesting company.
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
        $company   = $request->getAttribute('company');
        $companies = $this->repository->getAllByParentId($company->id);

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
     * Retrieves all public information from a single Company.
     *
     * @apiEndpointResponse 200 schema/company/getOne.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @throws App\Exception\NotFound
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
     * Creates a new child company for the requesting company.
     *
     * @apiEndpointRequiredParam body string name NewCo. Company name
     * @apiEndpointResponse 201 schema/company/createNew.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function createNew(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $company = $request->getAttribute('company');

        $command = $this->commandFactory->create('Company\\CreateNew');
        $command
            ->setParameters($request->getParsedBody())
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
     * Deletes all child companies that belong to the requesting company.
     *
     * @apiEndpointResponse 200 schema/company/deleteAll.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function deleteAll(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $company = $request->getAttribute('company');

        $command = $this->commandFactory->create('Company\\DeleteAll');
        $command->setParameter('parentId', $company->id);
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
     * Deletes the requesting company or a child company that belongs to it.
     *
     * @apiEndpointResponse 200 schema/company/deleteOne.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @throws App\Exception\NotFound
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function deleteOne(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $targetCompany = $request->getAttribute('targetCompany');

        $command = $this->commandFactory->create('Company\\DeleteOne');
        $command->setParameter('company', $targetCompany);
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
     * Updates the information for a single Company.
     *
     * @apiEndpointRequiredParam body string name NewName New Company name
     * @apiEndpointResponse 200 schema/company/updateOne.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @throws App\Exception\NotFound
     *
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @see App\Command\Company\UpdateOne
     */
    public function updateOne(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $targetCompany = $request->getAttribute('targetCompany');

        $command = $this->commandFactory->create('Company\\UpdateOne');
        $command
            ->setParameters($request->getParsedBody())
            ->setParameter('companyId', $targetCompany->id);
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
}
