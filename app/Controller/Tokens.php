<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Controller;

use App\Entity\User;
use App\Exception\AppException;
use App\Factory\Command;
use App\Repository\CompanyInterface;
use League\Tactician\CommandBus;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Handles requests to /token.
 */
class Tokens implements ControllerInterface {
    /**
     * Company Repository instance.
     *
     * @var \App\Repository\CompanyInterface
     */
    private $companyRepository;
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
     * @param \App\Repository\CompanyInterface $companyRepository
     * @param \League\Tactician\CommandBus    $commandBus
     * @param \App\Factory\Command             $commandFactory
     *
     * @return void
     */
    public function __construct(
        CompanyInterface $companyRepository,
        CommandBus $commandBus,
        Command $commandFactory
    ) {
        $this->companyRepository = $companyRepository;
        $this->commandBus        = $commandBus;
        $this->commandFactory    = $commandFactory;
    }

    /**
     * Created a new token data for a given source.
     *
     * @apiEndpointRequiredParam body string slug WRONG FORMAT HERE FIXME
     * @apiEndpointResponse 201 schema/token/tokenEntity.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @see \App\Repository\DBCompany::findBySlug
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function exchange(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $command = $this->commandFactory->create('Token\\Exchange');

        $slug = $request->getParsedBody()['slug'];

        if (! $slug) {
            throw new AppException('No slug provided');
        }

        $targetCompany = $this->companyRepository->findBySlug($slug);

        $command
            ->setParameter('user', $request->getAttribute('user'))
            ->setParameter('actingCompany', $request->getAttribute('company'))
            ->setParameter('targetCompany', $targetCompany)
            ->setParameter('credential', $request->getAttribute('credential'));

        $token = $this->commandBus->handle($command);

        $body = [
            'status' => true,
            'data'   => $token
        ];

        $command = $this->commandFactory->create('ResponseDispatch');
        $command
            ->setParameter('statusCode', 200)
            ->setParameter('request', $request)
            ->setParameter('response', $response)
            ->setParameter('body', $body);

        return $this->commandBus->handle($command);
    }
}
