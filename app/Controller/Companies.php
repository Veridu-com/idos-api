<?php
/**
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace App\Controller;

use App\Factory\Command;
use App\Repository\CompanyInterface;
use Jenssegers\Optimus\Optimus;
use League\Tactician\CommandBus;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Handles requests to /companies and /companies/{companySlug}.
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
     * List all child Companies that belongs to the Acting Company.
     *
     * @apiEndpointParam query int after Initial Company creation date (lower bound)
     * @apiEndpointParam query int before Final Company creation date (upper bound)
     * @apiEndpointParam query int page
     * @apiEndpointResponse 200 Company[]
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function listAll(ServerRequestInterface $request, ResponseInterface $response) {
        $actingCompany = $request->getAttribute('actingCompany');

        $companies = $this->repository->getAllByParentId($actingCompany->id);

        $body = [
            'data'    => $companies->toArray(),
            'updated' => (
                $companies->isEmpty() ? time() : strtotime($companies->max('updated_at'))
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
     * @apiRequiredParam path string companySlug
     * @apiEndpointResponse 200 Company
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @throws App\Exception\NotFound
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getOne(ServerRequestInterface $request, ResponseInterface $response) {
        $targetCompany = $request->getAttribute('targetCompany');

        $body = [
            'data'    => $targetCompany->toArray(),
            'updated' => strtotime($targetCompany->updated_at)
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
     * @apiEndpointResponse 201 Company
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function createNew(ServerRequestInterface $request, ResponseInterface $response) {
        $actingCompany = $request->getAttribute('actingCompany');

        $command = $this->commandFactory->create('Company\\CreateNew');
        $command
            ->setParameters($request->getParsedBody())
            ->setParameter('parentId', $actingCompany->id);
        $company = $this->commandBus->handle($command);

        $body = [
            'data' => $company
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
     * Deletes all child Companies that belongs to the Acting Company.
     *
     * @apiEndpointResponse 200 -
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function deleteAll(ServerRequestInterface $request, ResponseInterface $response) {
       $actingCompany = $request->getAttribute('actingCompany');

       $command = $this->commandFactory->create('Company\\DeleteAll', [$actingCompany->id]);
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
     * Deletes the Target Company, a child of the Acting Company.
     *
     * @apiEndpointRequiredParam path string companySlug
     * @apiEndpointResponse 200 -
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @throws App\Exception\NotFound
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function deleteOne(ServerRequestInterface $request, ResponseInterface $response) {
        $targetCompany = $request->getAttribute('targetCompany');

        $command = $this->commandFactory->create('Company\\DeleteOne');
        $command->setParameter('companyId', $targetCompany->id);
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
     * Updates the Target Company, a child of the Acting Company.
     *
     * @apiEndpointResponse 200 Company
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @throws App\Exception\NotFound
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function updateOne(ServerRequestInterface $request, ResponseInterface $response) {
        $targetCompany = $request->getAttribute('targetCompany');

        $command = $this->commandFactory->create('Company\\UpdateOne');
        $command
            ->setParameters($request->getParsedBody())
            ->setParameter('companyId', $targetCompany->id);
        $targetCompany = $this->commandBus->handle($command);

        $body = [
            'data'    => $targetCompany,
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
