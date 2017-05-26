<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Controller\Profile;

use App\Controller\ControllerInterface;
use App\Factory\Command;
use App\Repository\Profile\RecommendationInterface;
use League\Tactician\CommandBus;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Handles requests to /profiles/{userName}/recommendation.
 */
class Recommendation implements ControllerInterface {
    /**
     * Recommendation Repository instance.
     *
     * @var \App\Repository\Profile\RecommendationInterface
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
     * @param \App\Repository\Profile\RecommendationInterface $repository
     * @param \League\Tactician\CommandBus                    $commandBus
     * @param \App\Factory\Command                            $commandFactory
     *
     * @return void
     */
    public function __construct(
        RecommendationInterface $repository,
        CommandBus $commandBus,
        Command $commandFactory
    ) {
        $this->repository       = $repository;
        $this->commandBus       = $commandBus;
        $this->commandFactory   = $commandFactory;
    }

    /**
     * Retrieves the profile recommendation.
     *
     * @apiEndpointResponse 200 schema/recommendation/getOne.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @see \App\Repository\DBRecommendation::findOne
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getOne(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $user = $request->getAttribute('targetUser');

        $recommendation = $this->repository->findOne($user->id);

        $body = [
            'data'    => $recommendation->toArray(),
            'updated' => $recommendation->updatedAt
        ];

        $command = $this->commandFactory->create('ResponseDispatch');
        $command
            ->setParameter('request', $request)
            ->setParameter('response', $response)
            ->setParameter('body', $body);

        return $this->commandBus->handle($command);
    }

    /**
     * Creates or updates the profile recommendation.
     *
     * @apiEndpointRequiredParam body string result pass Recommendation result
     * @apiEndpointRequiredParam body string passed rules-passed The rules that the profile have passed
     * @apiEndpointRequiredParam body string failed rules-failed The rules that the profile have failed to pass
     * @apiEndpointResponse 201 schema/recommendation/upsert.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function upsert(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $user        = $request->getAttribute('targetUser');
        $handler     = $request->getAttribute('handler');
        $company     = $request->getAttribute('company');
        $credential  = $request->getAttribute('credential');

        $command = $this->commandFactory->create('Profile\\Recommendation\\Upsert');
        $command
            ->setParameters($request->getParsedBody() ?: [])
            ->setParameter('user', $user)
            ->setParameter('handler', $handler)
            ->setParameter('company', $company)
            ->setParameter('credential', $credential);

        $feature = $this->commandBus->handle($command);

        $body = [
            'status' => true,
            'data'   => $feature->toArray()
        ];

        $command = $this->commandFactory->create('ResponseDispatch');
        $command
            ->setParameter('statusCode', isset($feature->updatedAt) ? 200 : 201)
            ->setParameter('request', $request)
            ->setParameter('response', $response)
            ->setParameter('body', $body);

        return $this->commandBus->handle($command);
    }
}
