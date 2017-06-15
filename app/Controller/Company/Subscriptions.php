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
 * Handles requests to /companies/{companySlug}/credentials/{credentialPubKey}/subscriptions and /companies/{companySlug}/credentials/{credentialPubKey}/subscriptions/{subscriptionId}.
 */
class Subscriptions implements ControllerInterface {
    /**
     * Subscription Repository instance.
     *
     * @var \App\Repository\Company\SubscriptionInterface
     */
    private $repository;
    /**
     * Credential Repository instance.
     *
     * @var \App\Repository\Company\CredentialInterface
     */
    private $credentialRepository;
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
     * @param \App\Repository\Company\SubscriptionInterface $repository
     * @param \App\Repository\Company\CredentialInterface   $credentialRepository
     * @param \League\Tactician\CommandBus                  $commandBus
     * @param \App\Factory\Command                          $commandFactory
     *
     * @return void
     */
    public function __construct(
        SubscriptionInterface $repository,
        CredentialInterface $credentialRepository,
        CommandBus $commandBus,
        Command $commandFactory
    ) {
        $this->repository               = $repository;
        $this->credentialRepository     = $credentialRepository;
        $this->commandBus               = $commandBus;
        $this->commandFactory           = $commandFactory;
    }

    /**
     * Lists all Subscriptions that belongs to the given credential.
     *
     * @apiEndpointResponse 200 schema/credential/listAll.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function listAll(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $identity = $request->getAttribute('identity');

        $subscriptions = $this->repository->getByCredentialId($identity->id);

        $body = [
            'status'  => true,
            'data'    => $subscriptions->toArray(),
            'updated' => (
                $subscriptions->isEmpty() ? time() : max($subscriptions->max('updatedAt'), $subscriptions->max('createdAt'))
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
     * Creates a new Subscription for the credential.
     *
     * @apiEndpointRequiredParam body string categorySlug firstName Subscription categorySlug
     * @apiEndpointResponse 201 schema/credential/createNew.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @see \App\Handler\Subscription::handleCreateNew
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function createNew(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $identity = $request->getAttribute('identity');

        $credential = $this->credentialRepository->findByPubKey($request->getAttribute('pubKey'));

        $command = $this->commandFactory->create('Company\Subscription\CreateNew');

        $command
            ->setParameters($request->getParsedBody() ?: [])
            ->setParameter('credential', $credential)
            ->setParameter('identity', $identity);

        $subscription = $this->commandBus->handle($command);

        $body = [
            'data' => $subscription->toArray()
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
     * Deletes one Subscription of the credential based on the Public Key.
     *
     * @apiEndpointResponse 200 schema/credential/deleteOne.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @see \App\Repository\DBSubscription::findByPubKey
     * @see \App\Handler\Subscription::handleDeleteOne
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function deleteOne(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $subscriptionId = $request->getAttribute('decodedSubscriptionId');
        $identity       = $request->getAttribute('identity');

        $command = $this->commandFactory->create('Company\Subscription\DeleteOne');
        $command
            ->setParameter('identity', $identity)
            ->setParameter('subscriptionId', $subscriptionId);

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
