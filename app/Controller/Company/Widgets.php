<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Controller\Company;

use App\Controller\ControllerInterface;
use App\Factory\Command;
use App\Repository\Company\WidgetInterface;
use League\Tactician\CommandBus;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Handles requests to /companies/{companySlug}/widgets.
 */
class Widgets implements ControllerInterface {
    /**
     * Widget Repository instance.
     *
     * @var \App\Repository\Company\WidgetInterface
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
     * @param \App\Repository\Company\WidgetInterface $repository
     * @param \League\Tactician\CommandBus            $commandBus
     * @param \App\Factory\Command                    $commandFactory
     *
     * @return void
     */
    public function __construct(
        WidgetInterface $repository,
        CommandBus $commandBus,
        Command $commandFactory
    ) {
        $this->repository           = $repository;
        $this->commandBus           = $commandBus;
        $this->commandFactory       = $commandFactory;
    }

    /**
     * Lists all widgets of the target Company.
     *
     * @apiEndpointResponse 200 schema/widget/listAll.json
     *
     * @param \Psr\ServerRequestInterface $request
     * @param \Psr\ResponseInterface      $response
     *
     * @see \App\Repository\DBWidget::getAllByCredentialPubKey
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function listAll(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $credentialPubKey = $request->getAttribute('pubKey');
        $targetCompany    = $request->getAttribute('targetCompany');

        $widgets = $this->repository->getByCompanyId($targetCompany->id);

        $body = [
            'data'    => $widgets->toArray(),
            'updated' => (
                $widgets->isEmpty() ? time() : max($widgets->max('updatedAt'), $widgets->max('createdAt'))
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
     * Retrieves a widget from the given credential.
     *
     * @apiEndpointResponse 200 schema/widget/widgetEntity.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getOne(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $company          = $request->getAttribute('targetCompany');
        $widgetHash       = $request->getAttribute('widgetHash');

        $widget = $this->repository->findByHash($widgetHash);

        $body = [
            'data' => $widget->toArray()
        ];

        $command = $this->commandFactory->create('ResponseDispatch');
        $command
            ->setParameter('request', $request)
            ->setParameter('response', $response)
            ->setParameter('body', $body);

        return $this->commandBus->handle($command);
    }

    /**
     * Creates a new widget for the given credential.
     *
     * @apiEndpointRequiredParam body string trigger company.create Trigger
     * @apiEndpointRequiredParam body string url http://test.com/example.php Url
     * @apiEndpointRequiredParam body boolean subscribed false Subscribed
     * @apiEndpointResponse 201 schema/widget/widgetEntity.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @see \App\Handler\Widget::handleCreateNew
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function createNew(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $company           = $request->getAttribute('targetCompany');
        $identity          = $request->getAttribute('identity');

        $command = $this->commandFactory->create('Company\\Widget\\CreateNew');
        $command
            ->setParameter('company', $company)
            ->setParameter('identity', $identity)
            ->setParameters($request->getParsedBody());

        $widget = $this->commandBus->handle($command);

        $body = [
            'status' => true,
            'data'   => $widget->toArray()
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
     * Updates a widget from the given credential.
     *
     * @apiEndpointRequiredParam body string trigger company.create Trigger
     * @apiEndpointRequiredParam body string url http://test.com/example.php Url
     * @apiEndpointRequiredParam body boolean subscribed false Subscribed
     * @apiEndpointResponse 200 schema/widget/updateOne.json
     *
     * @param \Psr\ServerRequestInterface $request
     * @param \Psr\ResponseInterface      $response
     *
     * @see \App\Handler\Widget::handleUpdateOne
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function updateOne(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $company          = $request->getAttribute('targetCompany');
        $widgetHash       = $request->getAttribute('widgetHash');
        $identity         = $request->getAttribute('identity');

        $command = $this->commandFactory->create('Company\\Widget\\UpdateOne');
        $command
            ->setParameter('identity', $identity)
            ->setParameter('hash', $widgetHash)
            ->setParameters($request->getParsedBody() ?: []);

        $widget = $this->commandBus->handle($command);

        $body = [
            'data'    => $widget->toArray(),
            'updated' => $widget->updated_at
        ];

        $command = $this->commandFactory->create('ResponseDispatch');
        $command
            ->setParameter('request', $request)
            ->setParameter('response', $response)
            ->setParameter('body', $body);

        return $this->commandBus->handle($command);
    }

    /**
     * Deletes a widget from the given credential.
     *
     * @apiEndpointResponse 200 schema/widget/deleteOne.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @see \App\Handler\Widget::handleDeleteOne
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function deleteOne(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $widgetHash = $request->getAttribute('widgetHash');
        $identity   = $request->getAttribute('identity');

        $command = $this->commandFactory->create('Company\\Widget\\DeleteOne');
        $command
            ->setParameter('hash', $widgetHash)
            ->setParameter('identity', $identity);

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
