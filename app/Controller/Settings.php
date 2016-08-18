<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types=1);

namespace App\Controller;

use App\Factory\Command;
use App\Repository\SettingInterface;
use League\Tactician\CommandBus;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Handles requests to /companies/{companySlug}/settings.
 */
class Settings implements ControllerInterface {
    /**
     * Setting Repository instance.
     *
     * @var App\Repository\SettingInterface
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
     * @param App\Repository\SettingInterface $repository
     * @param \League\Tactician\CommandBus    $commandBus
     * @param App\Factory\Command             $commandFactory
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
        $result        = $this->repository->getAllByCompanyId($targetCompany->id, $request->getQueryParams());

        $data = $result['collection'];

        $body = [
            'data'       => $data->toArray(),
            'pagination' => $result['pagination'],
            'updated'    => (
                $data->isEmpty() ? time() : $data->max('updated_at')
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
     * Lists all Settings that belongs to the Target Company and has the given section.
     *
     * @apiEndpointParam query int page 10|1 Current page
     * @apiEndpointResponse 200 schema/setting/listAllFromSection.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function listAllFromSection(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $targetCompany = $request->getAttribute('targetCompany');
        $section       = $request->getAttribute('section');
        $settings      = $this->repository->getAllByCompanyIdAndSection($targetCompany->id, $section);

        $body = [
            'data'    => $settings->toArray(),
            'updated' => (
                $settings->isEmpty() ? time() : $settings->max('updated_at')
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
        $targetCompany = $request->getAttribute('targetCompany');
        $section       = $request->getAttribute('section');
        $propName      = $request->getAttribute('property');
        $setting       = $this->repository->findOne($targetCompany->id, $section, $propName);

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
     * Creates a new Setting for the Target Company.
     *
     * @apiEndpointRequiredParam body string section XXX Section name
     * @apiEndpointRequiredParam body string property YYY Property name
     * @apiEndpointRequiredParam body string value ZZZ Property value
     * @apiEndpointResponse 201 schema/setting/createNew.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function createNew(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $targetCompany = $request->getAttribute('targetCompany');

        $command = $this->commandFactory->create('Setting\\CreateNew');
        $command
            ->setParameters($request->getParsedBody())
            ->setParameter('companyId', $targetCompany->id);

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
     * Deletes all Settings that belongs to the Target Company.
     *
     * @apiEndpointResponse 200 schema/setting/deleteAll.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function deleteAll(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $targetCompany = $request->getAttribute('targetCompany');

        $command = $this->commandFactory->create('Setting\\DeleteAll');
        $command->setParameter('companyId', $targetCompany->id);

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

    /**
     * Deletes one Setting of the Target Company based on path paramaters section and property.
     *
     * @apiEndpointResponse 200 schema/setting/deleteOne.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function deleteOne(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $targetCompany = $request->getAttribute('targetCompany');
        $section       = $request->getAttribute('section');
        $property      = $request->getAttribute('property');

        $command = $this->commandFactory->create('Setting\\DeleteOne');
        $command
            ->setParameter('companyId', $targetCompany->id)
            ->setParameter('section', $section)
            ->setParameter('property', $property);

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

    /**
     * Updates one Setting of the Target Company based on path paramaters section and property.
     *
     * @apiEndpointRequiredParam body string value ZZZ Property value
     * @apiEndpointResponse 200 schema/setting/updateOne.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function updateOne(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $targetCompany = $request->getAttribute('targetCompany');
        $section       = $request->getAttribute('section');
        $propName      = $request->getAttribute('property');

        $command = $this->commandFactory->create('Setting\\UpdateOne');
        $command
            ->setParameters($request->getParsedBody())
            ->setParameter('sectionNameId', $section)
            ->setParameter('propNameId', $propName)
            ->setParameter('companyId', $targetCompany->id);

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
}
