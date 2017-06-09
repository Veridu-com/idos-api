<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Controller\Company;

use App\Controller\ControllerInterface;
use App\Exception\NotFound;
use App\Factory\Command;
use App\Repository\Company\CredentialInterface;
use App\Repository\Company\HookInterface;
use League\Tactician\CommandBus;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Handles requests to /companies/{companySlug}/credentials/{pubKey}/hooks and /companies/{companySlug}/credentials/{pubKey}/hooks/{hookId}.
 */
class Hooks implements ControllerInterface {
    /**
     * Hook Repository instance.
     *
     * @var \App\Repository\Company\HookInterface
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
     * @param \App\Repository\Company\HookInterface       $repository
     * @param \App\Repository\Company\CredentialInterface $credentialRepository
     * @param \League\Tactician\CommandBus                $commandBus
     * @param \App\Factory\Command                        $commandFactory
     *
     * @return void
     */
    public function __construct(
        HookInterface $repository,
        CredentialInterface $credentialRepository,
        CommandBus $commandBus,
        Command $commandFactory
    ) {
        $this->repository           = $repository;
        $this->credentialRepository = $credentialRepository;
        $this->commandBus           = $commandBus;
        $this->commandFactory       = $commandFactory;
    }

    /**
     * Lists all hooks associated with given credential.
     *
     * @apiEndpointResponse 200 schema/hook/listAll.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @see \App\Repository\DBHook::findByPubKey
     * @see \App\Repository\DBHook::getAllByCredentialPubKeyAndCompanyId
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function listAll(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $credentialPubKey = $request->getAttribute('pubKey');
        $targetCompany    = $request->getAttribute('targetCompany');

        $credential = $this->credentialRepository->findByPubKey($credentialPubKey);

        if ($credential->companyId !== $targetCompany->id) {
            throw new NotFound();
        }

        $hooks = $this->repository->getAllByCredentialPubKeyAndCompanyId($credentialPubKey, $targetCompany->id);

        $body = [
            'data'    => $hooks->toArray(),
            'updated' => (
                $hooks->isEmpty() ? time() : max($hooks->max('updatedAt'), $hooks->max('createdAt'))
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
     * Retrieves a hook from the given credential.
     *
     * @apiEndpointResponse 200 schema/hook/hookEntity.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getOne(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $company          = $request->getAttribute('targetCompany');
        $hookId           = (int) $request->getAttribute('decodedHookId');
        $credentialPubKey = $request->getAttribute('pubKey');

        $command = $this->commandFactory->create('Company\\Hook\\GetOne');
        $command
            ->setParameter('company', $company)
            ->setParameter('hookId', $hookId)
            ->setParameter('credentialPubKey', $credentialPubKey);

        $hook = $this->commandBus->handle($command);

        $body = [
            'data' => $hook->toArray()
        ];

        $command = $this->commandFactory->create('ResponseDispatch');
        $command
            ->setParameter('request', $request)
            ->setParameter('response', $response)
            ->setParameter('body', $body);

        return $this->commandBus->handle($command);
    }

    /**
     * Creates a new hook for the given credential.
     *
     * @apiEndpointRequiredParam body string trigger company.create Trigger
     * @apiEndpointRequiredParam body string url http://test.com/example.php Url
     * @apiEndpointRequiredParam body boolean subscribed false Subscribed
     * @apiEndpointResponse 201 schema/hook/createNew.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @see \App\Handler\Hook::handleCreateNew
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function createNew(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $company          = $request->getAttribute('targetCompany');
        $credentialPubKey = $request->getAttribute('pubKey');
        $identity         = $request->getAttribute('identity');

        $command = $this->commandFactory->create('Company\\Hook\\CreateNew');
        $command
            ->setParameter('identity', $identity)
            ->setParameter('credentialPubKey', $credentialPubKey)
            ->setParameter('company', $company)
            ->setParameters($request->getParsedBody() ?: []);

        $hook = $this->commandBus->handle($command);

        $body = [
            'status' => true,
            'data'   => $hook->toArray()
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
     * Updates a hook from the given credential.
     *
     * @apiEndpointRequiredParam body string trigger company.create Trigger
     * @apiEndpointRequiredParam body string url http://test.com/example.php Url
     * @apiEndpointRequiredParam body boolean subscribed false Subscribed
     * @apiEndpointResponse 200 schema/hook/updateOne.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @see \App\Handler\Hook::handleUpdateOne
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function updateOne(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $hookId           = (int) $request->getAttribute('decodedHookId');
        $company          = $request->getAttribute('targetCompany');
        $credentialPubKey = $request->getAttribute('pubKey');
        $identity         = $request->getAttribute('identity');

        $command = $this->commandFactory->create('Company\\Hook\\UpdateOne');
        $command
            ->setParameter('identity', $identity)
            ->setParameter('hookId', $hookId)
            ->setParameter('company', $company)
            ->setParameter('credentialPubKey', $credentialPubKey)
            ->setParameters($request->getParsedBody() ?: []);

        $hook = $this->commandBus->handle($command);

        $body = [
            'data'    => $hook->toArray(),
            'updated' => $hook->updated_at
        ];

        $command = $this->commandFactory->create('ResponseDispatch');
        $command
            ->setParameter('request', $request)
            ->setParameter('response', $response)
            ->setParameter('body', $body);

        return $this->commandBus->handle($command);
    }

    /**
     * Deletes a hook from the given credential.
     *
     * @apiEndpointResponse 200 schema/hook/deleteOne.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @see \App\Handler\Hook::handleDeleteOne
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function deleteOne(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $company          = $request->getAttribute('targetCompany');
        $credentialPubKey = $request->getAttribute('pubKey');
        $identity         = $request->getAttribute('identity');

        $command = $this->commandFactory->create('Company\\Hook\\DeleteOne');
        $command
            ->setParameter('identity', $identity)
            ->setParameter('hookId', $request->getAttribute('decodedHookId'))
            ->setParameter('credentialPubKey', $credentialPubKey)
            ->setParameter('company', $company);

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
