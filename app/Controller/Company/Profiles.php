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
use App\Repository\Profile\GateInterface;
use App\Repository\Profile\ReviewInterface;
use App\Repository\Profile\WarningInterface;
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
     * @var App\Repository\UserInterface
     */
    private $repository;
    /**
     * ReviewRepository instance.
     *
     * @var App\Repository\ReviewInterface
     */
    private $reviewRepository;
    /**
     * WarningRepository instance.
     *
     * @var App\Repository\WarningInterface
     */
    private $warningRepository;
    /**
     * GateRepository instance.
     *
     * @var App\Repository\GateInterface
     */
    private $gateRepository;
    /**
     * AttributeRepository instance.
     *
     * @var App\Repository\AttributeInterface
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
     * @var App\Factory\Command
     */
    private $commandFactory;

    /**
     * Class constructor.
     *
     * @param App\Repository\UserInterface      $repository
     * @param App\Repository\ReviewInterface    $reviewRepository
     * @param App\Repository\WarningInterface   $warningRepository
     * @param App\Repository\GateInterface      $gateRepository
     * @param App\Repository\AttributeInterface $attributeRepository
     * @param \League\Tactician\CommandBus      $commandBus
     * @param App\Factory\Command               $commandFactory
     *
     * @return void
     */
    public function __construct(
        UserInterface $repository,
        ReviewInterface $reviewRepository,
        WarningInterface $warningRepository,
        GateInterface $gateRepository,
        AttributeInterface $attributeRepository,
        CommandBus $commandBus,
        Command $commandFactory
    ) {
        $this->repository          = $repository;
        $this->reviewRepository    = $reviewRepository;
        $this->warningRepository   = $warningRepository;
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
            $reviews = $this->reviewRepository->findByUserId($profile->id);
            $warnings = $this->warningRepository->findByUserId($profile->id);
            $gates    = $this->gateRepository->findByUserId($profile->id);

            $firstNames      = $this->attributeRepository->getAllByUserIdAndNames($profile->id, ['name' => 'firstname']);
            $firstNamesArray = [];
            foreach ($firstNames as $firstName) {
                $firstNamesArray[] = $firstName->value;
            }

            $middleNames      = $this->attributeRepository->getAllByUserIdAndNames($profile->id, ['name' => 'middlename']);
            $middleNamesArray = [];
            foreach ($middleNames as $middleName) {
                $middleNamesArray[] = $middleName->value;
            }

            $lastNames      = $this->attributeRepository->getAllByUserIdAndNames($profile->id, ['name' => 'lastname']);
            $lastNamesArray = [];
            foreach ($lastNames as $lastName) {
                $lastNamesArray[] = $lastName->value;
            }

            $data[] = array_merge(
                $profile->toArray(),
                ['reviews'     => $reviews->toArray()],
                ['warnings'    => $warnings->toArray()],
                ['gates'       => $gates->toArray()],
                ['firstnames'  => $firstNamesArray],
                ['middlenames' => $middleNamesArray],
                ['lastnames'   => $lastNamesArray]
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
     * @throws App\Exception\NotFound\CompanyProfileException
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getOne(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $userId = $request->getAttribute('decodedUserId');

        $profile = $this->repository->find($userId);

        $warnings = $this->warningRepository->findByUserId($profile->id);
        $gates    = $this->gateRepository->findByUserId($profile->id);

        $firstNames      = $this->attributeRepository->getAllByUserIdAndNames($profile->id, ['name' => 'firstname']);
        $firstNamesArray = [];
        foreach ($firstNames as $firstName) {
            $firstNamesArray[] = $firstName->value;
        }

        $middleNames      = $this->attributeRepository->getAllByUserIdAndNames($profile->id, ['name' => 'middlename']);
        $middleNamesArray = [];
        foreach ($middleNames as $middleName) {
            $middleNamesArray[] = $middleName->value;
        }

        $lastNames      = $this->attributeRepository->getAllByUserIdAndNames($profile->id, ['name' => 'lastname']);
        $lastNamesArray = [];
        foreach ($lastNames as $lastName) {
            $lastNamesArray[] = $lastName->value;
        }

        $data = array_merge(
            $profile->toArray(),
            ['warnings'    => $warnings->toArray()],
            ['gates'       => $gates->toArray()],
            ['firstnames'  => $firstNamesArray],
            ['middlenames' => $middleNamesArray],
            ['lastnames'   => $lastNamesArray]
        );

        $body = [
            'data'    => $data,
            'updated' => $profile->updatedAt
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
     * @throws App\Exception\NotFound\CompanyProfileException
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function deleteOne(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface {
        $userId = $request->getAttribute('decodedUserId');

        $command = $this->commandFactory->create('Company\\Profile\\DeleteOne');
        $command->setParameter('userId', $userId);
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
