<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Controller\Company;

use App\Controller\ControllerInterface;
use App\Factory\Command;
use App\Repository\Profile\AttributeInterface;
use App\Repository\Profile\FlagInterface;
use App\Repository\Profile\GateInterface;
use App\Repository\Profile\ReviewInterface;
use App\Repository\Profile\SourceInterface;
use App\Repository\Profile\TagInterface;
use App\Repository\UserInterface;
use League\Tactician\CommandBus;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Handles requests to /companies/{companySlug}/profiles/ and /companies/{companySlug}/profiles/{userId}.
 */
class Profiles implements ControllerInterface {
    /**
     * UserRepository instance.
     *
     * @var \App\Repository\UserInterface
     */
    private $repository;
    /**
     * SourceRepository instance.
     *
     * @var \App\Repository\SourceInterface
     */
    private $sourceRepository;
    /**
     * TagRepository instance.
     *
     * @var \App\Repository\TagInterface
     */
    private $tagRepository;
    /**
     * ReviewRepository instance.
     *
     * @var \App\Repository\ReviewInterface
     */
    private $reviewRepository;
    /**
     * FlagRepository instance.
     *
     * @var \App\Repository\FlagInterface
     */
    private $flagRepository;
    /**
     * GateRepository instance.
     *
     * @var \App\Repository\GateInterface
     */
    private $gateRepository;
    /**
     * AttributeRepository instance.
     *
     * @var \App\Repository\AttributeInterface
     */
    private $attributeRepository;
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
     * @param \App\Repository\UserInterface      $repository
     * @param \App\Repository\SourceInterface    $sourceRepository
     * @param \App\Repository\TagInterface       $tagRepository
     * @param \App\Repository\ReviewInterface    $reviewRepository
     * @param \App\Repository\FlagInterface      $flagRepository
     * @param \App\Repository\GateInterface      $gateRepository
     * @param \App\Repository\AttributeInterface $attributeRepository
     * @param \League\Tactician\CommandBus       $commandBus
     * @param \App\Factory\Command               $commandFactory
     *
     * @return void
     */
    public function __construct(
        UserInterface $repository,
        SourceInterface $sourceRepository,
        TagInterface $tagRepository,
        ReviewInterface $reviewRepository,
        FlagInterface $flagRepository,
        GateInterface $gateRepository,
        AttributeInterface $attributeRepository,
        CommandBus $commandBus,
        Command $commandFactory
    ) {
        $this->repository          = $repository;
        $this->sourceRepository    = $sourceRepository;
        $this->tagRepository       = $tagRepository;
        $this->reviewRepository    = $reviewRepository;
        $this->flagRepository      = $flagRepository;
        $this->gateRepository      = $gateRepository;
        $this->attributeRepository = $attributeRepository;
        $this->commandBus          = $commandBus;
        $this->commandFactory      = $commandFactory;
    }

    /**
     * List all Profiles that belongs to the target Company.
     *
     * @apiEndpointResponse 200 schema/companyProfile/listAll.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function listAll(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $company = $request->getAttribute('targetCompany');

        $data     = [];
        $profiles = $this->repository->findByCompanyId($company->id);

        foreach ($profiles as $profile) {
            $sources    = $this->sourceRepository->getByUserId($profile->id);
            $tags       = $this->tagRepository->getByUserId($profile->id);
            $reviews    = $this->reviewRepository->getByUserId($profile->id);
            $flags      = $this->flagRepository->getByUserId($profile->id);
            $gates      = $this->gateRepository->getByUserId($profile->id);
            $attributes = $this->attributeRepository->findByUserId($profile->id);

            foreach ($gates as $gate) {
                $gateReview = null;
                foreach ($reviews as $review) {
                    if ($review->gateId === $gate->id) {
                        $gateReview = $review->toArray();
                        break;
                    }
                }

                $gate->review = $gateReview;
            }

            $profileSources = [];
            foreach ($sources as $source) {
                if (! in_array($source->name, $profileSources)) {
                    $profileSources[] = $source->name;
                }
            }

            $data[] = array_merge(
                $profile->toArray(),
                ['sources'     => $profileSources],
                ['tags'        => $tags->toArray()],
                ['flags'       => $flags->toArray()],
                ['gates'       => $gates->toArray()],
                ['attributes'  => $attributes->toArray()]
            );
        }

        $body = [
            'data'    => $data,
            'updated' => (
                $profiles->isEmpty() ? null : max($profiles->max('updatedAt'), $profiles->max('createdAt'))
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
     * Retrieves the user given by userId.
     *
     * @apiEndpointResponse 200 schema/companyProfile/getOne.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @throws \App\Exception\NotFound\CompanyProfileException
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getOne(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $userId = $request->getAttribute('decodedUserId');

        $data = [];

        $profile = $this->repository->find($userId);

        $attributes       = $this->attributeRepository->findByUserId($profile->id);
        $sources          = $this->sourceRepository->getByUserId($profile->id);
        $tags             = $this->tagRepository->getByUserId($profile->id);
        $reviews          = $this->reviewRepository->getByUserId($profile->id);
        $flags            = $this->flagRepository->getByUserId($profile->id);
        $gates            = $this->gateRepository->getByUserId($profile->id);

        foreach ($gates as $gate) {
            $gateReview = null;
            foreach ($reviews as $review) {
                if ($review->gateId === $gate->id) {
                    $gateReview = $review->toArray();
                    break;
                }
            }

            $gate->review = $gateReview;
        }

        $profileSources = [];
        foreach ($sources as $source) {
            if (! in_array($source->name, $profileSources)) {
                $profileSources[] = $source->name;
            }
        }

        $data = array_merge(
            $profile->toArray(),
            ['attributes'  => $attributes->toArray()],
            ['sources'     => $profileSources],
            ['tags'        => $tags->toArray()],
            ['flags'       => $flags->toArray()],
            ['gates'       => $gates->toArray()]
        );

        $body = [
            'data' => $data
        ];

        $command = $this->commandFactory->create('ResponseDispatch');
        $command
            ->setParameter('request', $request)
            ->setParameter('response', $response)
            ->setParameter('body', $body);

        return $this->commandBus->handle($command);

    }

    /**
     * Deletes the target company profile.
     *
     * @apiEndpointResponse 200 schema/companyProfile/deleteOne.json
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @throws \App\Exception\NotFound\CompanyProfileException
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function deleteOne(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $userId   = $request->getAttribute('decodedUserId');
        $identity = $request->getAttribute('identity');

        $command = $this->commandFactory->create('Company\\Profile\\DeleteOne');
        $command
            ->setParameter('identity', $identity)
            ->setParameter('userId', $userId);

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
}
