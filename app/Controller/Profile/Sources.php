<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Controller\Profile;

use App\Controller\ControllerInterface;
use App\Factory\Command;
use App\Repository\RepositoryInterface;
use League\Tactician\CommandBus;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Handles requests to /profiles/{userName}/sources and /profiles/{userName}/sources/{sourceId}.
 */
class Sources implements ControllerInterface {
    /**
     * Company Repository instance.
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
        $this->repository     = $repository;
        $this->commandBus     = $commandBus;
        $this->commandFactory = $commandFactory;
    }

    /**
     * List of all sources that belong to the requesting user.
     *
     * @apiEndpointParam query string after 2016-01-01|1070-01-01 Initial Company creation date (lower bound)
     * @apiEndpointParam query string before 2016-01-31|2016-12-31 Final Company creation date (upper bound)
     * @apiEndpointParam query int page 10|1 Current page
     * @apiEndpointResponse 200 schema/source/listAll.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @see \App\Repository\DBSource::getAllByUserId
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function listAll(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $user    = $request->getAttribute('targetUser');
        $sources = $this->repository->getByUserId($user->id);

        // @FIXME ACCESS MANAGEMENT REQUIRED!!
        //  How can an user access another's sources?

        $body = [
            'data'    => $sources->toArray(),
            'updated' => (
                $sources->isEmpty() ? time() : max($sources->max('updatedAt'), $sources->max('createdAt'))
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
     * Retrieves one Source.
     *
     * @apiEndpointResponse 200 schema/source/getOne.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @see \App\Repository\DBSource::findOne
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getOne(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $credential = $request->getAttribute('credential');
        $user       = $request->getAttribute('targetUser');
        $sourceId   = (int) $request->getAttribute('decodedSourceId');

        $command = $this->commandFactory->create('Profile\Source\GetOne');
        $command
            ->setParameters($request->getQueryParams() ?: [])
            ->setParameter('credential', $credential)
            ->setParameter('user', $user)
            ->setParameter('sourceId', $sourceId);

        $source = $this->commandBus->handle($command);

        $body = [
            'data' => $source->toArray()
        ];

        $command = $this->commandFactory->create('ResponseDispatch');
        $command
            ->setParameter('request', $request)
            ->setParameter('response', $response)
            ->setParameter('body', $body);

        return $this->commandBus->handle($command);
    }

    /**
     * Creates a new Source for the acting User.
     *
     * All source related data is packed on TAGS payload field.
     *  - oAuth 1.x should carry `access_token` and `token_secret`.
     *  - oAuth 2.x should carry `access_token` and optionally `token_refresh`.
     *  - E-mail should carry `email_address` and can optionally an `otp_check` flag for OTP verification.
     *  - Phone should carry `phone_number`, `country_code` and optionally an `otp_check` flag for OTP verification.
     *  - Submitted can carry as many fields as wanted and optionally a `cra_check` flag for CRA verification.
     *
     * @apiEndpointParam body string name twitter Source name
     * @apiEndpointParam body string tags {"otp_check": "email"} Source's new tags
     * @apiEndpointResponse 201 schema/source/createNew.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @see \App\Handler\Source::handleCreateNew
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function createNew(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $user       = $request->getAttribute('targetUser');
        $credential = $request->getAttribute('credential');

        $command = $this->commandFactory->create('Profile\Source\CreateNew');
        $command
            ->setParameters($request->getParsedBody() ?: [])
            ->setParameter('credential', $credential)
            ->setParameter('user', $user)
            ->setParameter('ipaddr', $request->getAttribute('ip_address'));

        $source = $this->commandBus->handle($command);
        $body   = [
            'data' => $source->toArray()
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
     * Updates one Source of the acting User based on path paremeter source id.
     *
     * All source related data is packed on TAGS payload field.
     *  - E-mail can carry an `otp_check` flag for OTP verification.
     *  - SMS can carry an `otp_check` flag for OTP verification.
     *  - Submitted can carry a `cra_check` flag for CRA verification.
     *
     * @apiEndpointParam body string otpCode OTP Code check for One Time Password Verifications
     * @apiEndpointParam body string tags  {"otp_check": "email"} Source's new tags
     * @apiEndpointResponse 200 schema/source/updateOne.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @see \App\Repository\DBSource::findOne
     * @see \App\Handler\Source::handleUpdateOne
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function updateOne(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $user       = $request->getAttribute('targetUser');
        $credential = $request->getAttribute('credential');
        $sourceId   = (int) $request->getAttribute('decodedSourceId');

        $source = $this->repository->findOne($sourceId, $user->id);

        $command = $this->commandFactory->create('Profile\Source\UpdateOne');
        $command
            ->setParameters($request->getParsedBody() ?: [])
            ->setParameter('credential', $credential)
            ->setParameter('user', $user)
            ->setParameter('source', $source)
            ->setParameter('ipaddr', $request->getAttribute('ip_address'));

        $source = $this->commandBus->handle($command);

        $body = [
            'data'    => $source->toArray(),
            'updated' => $source->updatedAt
        ];

        $command = $this->commandFactory->create('ResponseDispatch');
        $command
            ->setParameter('request', $request)
            ->setParameter('response', $response)
            ->setParameter('body', $body);

        return $this->commandBus->handle($command);
    }

    /**
     * Deletes one Source of the acting User based on path parameter source id.
     *
     * @apiEndpointResponse 200 schema/source/deleteOne.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @see \App\Repository\DBSource::findOne
     * @see \App\Handler\Source::handleDeleteOne
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function deleteOne(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $user       = $request->getAttribute('targetUser');
        $credential = $request->getAttribute('credential');
        $sourceId   = (int) $request->getAttribute('decodedSourceId');

        $source = $this->repository->findOne($sourceId, $user->id);

        $command = $this->commandFactory->create('Profile\Source\DeleteOne');
        $command
            ->setParameter('credential', $credential)
            ->setParameter('user', $user)
            ->setParameter('source', $source)
            ->setParameter('ipaddr', $request->getAttribute('ip_address'));

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

    /**
     * Deletes all Sources that belongs to the acting User.
     *
     * @apiEndpointResponse 200 schema/service/deleteAll.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @see \App\Handler\Source::handleDeleteAll
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function deleteAll(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $user       = $request->getAttribute('targetUser');
        $credential = $request->getAttribute('credential');

        $command = $this->commandFactory->create('Profile\Source\DeleteAll');
        $command
            ->setParameter('credential', $credential)
            ->setParameter('user', $user)
            ->setParameter('ipaddr', $request->getAttribute('ip_address'));

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
