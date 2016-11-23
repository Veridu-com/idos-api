<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Controller\Company;

use App\Controller\ControllerInterface;
use App\Factory\Command;
use App\Repository\Company\SettingInterface;
use League\Tactician\CommandBus;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Handles requests to /companies/{companySlug}/settings and /companies/{companySlug}/settings/{settingId}.
 */
class Settings implements ControllerInterface {
    /**
     * Setting Repository instance.
     *
     * @var \App\Repository\Company\SettingInterface
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
     * @param \App\Repository\Company\SettingInterface $repository
     * @param \League\Tactician\CommandBus             $commandBus
     * @param \App\Factory\Command                     $commandFactory
     *
     * @return void
     */
    public function __construct(
        SettingInterface $repository,
        CommandBus $commandBus,
        Command $commandFactory
    ) {
        $this->repository     = $repository;
        $this->commandBus     = $commandBus;
        $this->commandFactory = $commandFactory;
    }

    /**
     * Lists all Settings that belongs to the Target Company.
     *
     * @apiEndpointParam query int page 10|1 Current page
     * @apiEndpointResponse 200 schema/setting/listAll.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function listAll(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $targetCompany = $request->getAttribute('targetCompany');

        $command = $this->commandFactory->create('Company\\Setting\\ListAll');
        $command
            ->setParameter('hasParentAccess', $request->getAttribute('hasParentAccess'))
            ->setParameter('queryParams', $request->getQueryParams())
            ->setParameter('company', $targetCompany);

        $result   = $this->commandBus->handle($command);
        $entities = $result['collection'];

        $body = [
            'data'       => $entities->toArray(),
            'pagination' => $result['pagination'],
            'updated'    => (
                $entities->isEmpty() ? time() : max($entities->max('updatedAt'), $entities->max('createdAt'))
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
     * Retrieves one Setting of the Target Company based on path paramaters section and property.
     *
     * @apiEndpointResponse 200 schema/setting/getOne.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getOne(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $settingId = (int) $request->getAttribute('decodedSettingId');
        $identity  = $request->getAttribute('identity');
        $company   = $request->getAttribute('targetCompany');

        $command = $this->commandFactory->create('Company\\Setting\\GetOne');
        $command
            ->setParameter('company', $company)
            ->setParameter('settingId', $settingId)
            ->setParameter('hasParentAccess', $request->getAttribute('hasParentAccess'))
            ->setParameter('identity', $identity);

        $setting = $this->commandBus->handle($command);

        $body = [
            'data'    => $setting->toArray(),
            'updated' => max($setting->updatedAt, $setting->createdAt)
        ];

        $command = $this->commandFactory->create('ResponseDispatch');
        $command
            ->setParameter('request', $request)
            ->setParameter('response', $response)
            ->setParameter('body', $body);

        return $this->commandBus->handle($command);
    }

    /**
     * Creates a new Setting for the Target Company.
     *
     * @apiEndpointRequiredParam body string section AppTokens Section name
     * @apiEndpointRequiredParam body string property  1abc7jdoxsaz.facebook.key  Property name
     * @apiEndpointRequiredParam body string value 492361674b Property value
     * @apiEndpointParam body boolean protected true Protected value
     * @apiEndpointResponse 201 schema/setting/createNew.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @see \App\Handler\Settings::handleCreateNew
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function createNew(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $company  = $request->getAttribute('targetCompany');
        $identity = $request->getAttribute('identity');

        $command = $this->commandFactory->create('Company\\Setting\\CreateNew');
        $command
            ->setParameters($request->getParsedBody() ?: [])
            ->setParameter('identity', $identity)
            ->setParameter('company', $company);

        $setting = $this->commandBus->handle($command);

        $body = [
            'status' => true,
            'data'   => $setting->toArray()
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
     * Updates one Setting of the Target Company based on path paramaters section and property.
     *
     * @apiEndpointRequiredParam body string value 492361674b Property value
     * @apiEndpointResponse 200 schema/setting/updateOne.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @see \App\Handler\Setting::handleUpdateOne
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function updateOne(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $settingId = $request->getAttribute('decodedSettingId');
        $identity  = $request->getAttribute('identity');

        $command = $this->commandFactory->create('Company\\Setting\\UpdateOne');
        $command
            ->setParameters($request->getParsedBody() ?: [])
            ->setParameter('identity', $identity)
            ->setParameter('settingId', $settingId);

        $setting = $this->commandBus->handle($command);

        $body = [
            'data'    => $setting->toArray(),
            'updated' => $setting->updated_at
        ];

        $command = $this->commandFactory->create('ResponseDispatch');
        $command
            ->setParameter('request', $request)
            ->setParameter('response', $response)
            ->setParameter('body', $body);

        return $this->commandBus->handle($command);
    }

    /**
     * Deletes one Setting of the Target Company based on path paramaters section and property.
     *
     * @apiEndpointResponse 200 schema/setting/deleteOne.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @see \App\Handler\Setting::handleDeleteOne
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function deleteOne(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $settingId = $request->getAttribute('decodedSettingId');
        $identity  = $request->getAttribute('identity');

        $command = $this->commandFactory->create('Company\\Setting\\DeleteOne');
        $command
            ->setParameter('identity', $identity)
            ->setParameter('settingId', $settingId);

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
     * Deletes all Settings that belongs to the Target Company.
     *
     * @apiEndpointResponse 200 schema/setting/deleteAll.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @see \App\Handler\Settings::handleDeleteAll
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function deleteAll(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $company  = $request->getAttribute('company');
        $identity = $request->getAttribute('identity');

        $command = $this->commandFactory->create('Company\\Setting\\DeleteAll');
        $command
            ->setParameter('identity', $identity)
            ->setParameter('companyId', $company->id);

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
