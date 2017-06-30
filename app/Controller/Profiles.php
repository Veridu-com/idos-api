<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Controller;

use App\Exception\NotFound;
use App\Factory\Command;
use App\Repository\RepositoryInterface;
use League\Tactician\CommandBus;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Handles requests to /profiles.
 */
class Profiles implements ControllerInterface {
    /**
     * Profile Repository instance.
     *
     * @var \App\Repository\RepositoryInterface
     */
    private $repository;
    /**
     * Attribute Repository instance.
     *
     * @var \App\Repository\RepositoryInterface
     */
    private $attributeRepository;
    /**
     * Candidate Repository instance.
     *
     * @var \App\Repository\RepositoryInterface
     */
    private $candidateRepository;
    /**
     * Score Repository instance.
     *
     * @var \App\Repository\RepositoryInterface
     */
    private $scoreRepository;
    /**
     * Source Repository instance.
     *
     * @var \App\Repository\RepositoryInterface
     */
    private $sourceRepository;
    /**
     * Gate Repository instance.
     *
     * @var \App\Repository\RepositoryInterface
     */
    private $gateRepository;
    /**
     * Flag Repository instance.
     *
     * @var \App\Repository\RepositoryInterface
     */
    private $flagRepository;
    /**
     * Recommendation Repository instance.
     *
     * @var \App\Repository\RepositoryInterface
     */
    private $recommendationRepository;
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
     * @param \App\Repository\RepositoryInterface $attributeRepository
     * @param \App\Repository\RepositoryInterface $candidateRepository
     * @param \App\Repository\RepositoryInterface $scoreRepository
     * @param \App\Repository\RepositoryInterface $sourceRepository
     * @param \App\Repository\RepositoryInterface $gateRepository
     * @param \App\Repository\RepositoryInterface $flagRepository
     * @param \App\Repository\RepositoryInterface $recommendationRepository
     * @param \League\Tactician\CommandBus        $commandBus
     * @param \App\Factory\Command                $commandFactory
     *
     * @return void
     */
    public function __construct(
        RepositoryInterface $repository,
        RepositoryInterface $attributeRepository,
        RepositoryInterface $candidateRepository,
        RepositoryInterface $scoreRepository,
        RepositoryInterface $sourceRepository,
        RepositoryInterface $gateRepository,
        RepositoryInterface $flagRepository,
        RepositoryInterface $recommendationRepository,
        CommandBus $commandBus,
        Command $commandFactory
    ) {
        $this->repository                = $repository;
        $this->attributeRepository       = $attributeRepository;
        $this->candidateRepository       = $candidateRepository;
        $this->scoreRepository           = $scoreRepository;
        $this->sourceRepository          = $sourceRepository;
        $this->gateRepository            = $gateRepository;
        $this->flagRepository            = $flagRepository;
        $this->recommendationRepository  = $recommendationRepository;
        $this->commandBus                = $commandBus;
        $this->commandFactory            = $commandFactory;
    }

    /**
     * Retrieves a single profile.
     *
     * @apiEndpointResponse 200 schema/profile/getOne.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @see \App\Repository\Profile\CandidateInterface::findByUserId
     * @see \App\Repository\Profile\ScoreInterface::getByUserId
     * @see \App\Repository\Profile\SourceInterface::getByUserId
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getOne(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $user = $request->getAttribute('targetUser');

        $attributes = $this->attributeRepository->getAllByUserIdAndNames($user->id);
        $candidates = $this->candidateRepository->findByUserId($user->id);
        $scores     = $this->scoreRepository->getByUserId($user->id);
        $sources    = $this->sourceRepository->getByUserId($user->id);
        $gates      = $this->gateRepository->getByUserId($user->id);
        $flags      = $this->flagRepository->getByUserId($user->id);

        try {
            $recommendation = $this->recommendationRepository->findOne($user->id)->toArray();
        } catch (NotFound $exception) {
            $recommendation = null;
        }

        $data = [
            'username'       => $user->username,
            'attributes'     => $attributes->toArray(),
            'candidates'     => $candidates->toArray(),
            'scores'         => $scores->toArray(),
            'gates'          => $gates->toArray(),
            'sources'        => $sources->toArray(),
            'flags'          => $flags->toArray(),
            'recommendation' => $recommendation,
            'created_at'     => $user->createdAt
        ];

        $body = [
            'data'    => $data,
            'updated' => max(
                $user->updatedAt,
                $attributes->max('updatedAt'),
                $candidates->max('updatedAt'),
                $scores->max('updatedAt'),
                $sources->max('updatedAt')
            )
        ];

        $command = $this->commandFactory->create('ResponseDispatch');
        $command
            ->setParameter('request', $request)
            ->setParameter('response', $response)
            ->setParameter('body', $body);

        return $this->commandBus->handle($command);
    }
}
