<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Handler;

use App\Command\Company\CreateNew;
use App\Command\Company\DeleteOne;
use App\Command\Company\Setup;
use App\Command\Company\UpdateOne;
use App\Entity\Company as CompanyEntity;
use App\Entity\HandlerService;
use App\Exception\Create;
use App\Exception\NotFound;
use App\Exception\Update;
use App\Exception\Validate;
use App\Factory\Event;
use App\Repository\CompanyInterface;
use App\Repository\HandlerServiceInterface;
use App\Repository\ServiceInterface;
use App\Validator\Company as CompanyValidator;
use Defuse\Crypto\Key;
use Illuminate\Support\Collection;
use Interop\Container\ContainerInterface;
use League\Event\Emitter;
use Respect\Validation\Exceptions\ValidationException;

/**
 * Handles Company commands.
 */
class Company implements HandlerInterface {
    /**
     * Company Repository instance.
     *
     * @var \App\Repository\CompanyInterface
     */
    private $repository;
    /**
     * Service Repository instance.
     *
     * @var \App\Repository\ServiceInterface
     */
    private $serviceRepository;
    /**
     * Handler Service Repository instance.
     *
     * @var \App\Repository\HandlerServiceInterface
     */
    private $handlerServiceRepository;
    /**
     * Company Validator instance.
     *
     * @var \App\Validator\Company
     */
    private $validator;
    /**
     * Event factory instance.
     *
     * @var \App\Factory\Event
     */
    private $eventFactory;
    /**
     * Event emitter instance.
     *
     * @var \League\Event\Emitter
     */
    private $emitter;

    /**
     * {@inheritdoc}
     */
    public static function register(ContainerInterface $container) : void {
        $container[self::class] = function (ContainerInterface $container) : HandlerInterface {
            $repositoryFactory = $container->get('repositoryFactory');

            return new \App\Handler\Company(
                $repositoryFactory
                    ->create('Company'),
                $repositoryFactory
                    ->create('Service'),
                $repositoryFactory
                    ->create('HandlerService'),
                $container
                    ->get('validatorFactory')
                    ->create('Company'),
                $container
                    ->get('eventFactory'),
                $container
                    ->get('eventEmitter')
            );
        };
    }

    /**
     * Class constructor.
     *
     * @param \App\Repository\CompanyInterface        $repository
     * @param \App\Repository\ServiceInterface        $serviceRepository
     * @param \App\Repository\HandlerServiceInterface $handlerServiceRepository
     * @param \App\Validator\Company                  $validator
     * @param \App\Factory\Event                      $eventFactory
     * @param \League\Event\Emitter                   $emitter
     *
     * @return void
     */
    public function __construct(
        CompanyInterface $repository,
        ServiceInterface $serviceRepository,
        HandlerServiceInterface $handlerServiceRepository,
        CompanyValidator $validator,
        Event $eventFactory,
        Emitter $emitter
    ) {
        $this->repository                 = $repository;
        $this->serviceRepository          = $serviceRepository;
        $this->handlerServiceRepository   = $handlerServiceRepository;
        $this->validator                  = $validator;
        $this->eventFactory               = $eventFactory;
        $this->emitter                    = $emitter;
    }

    /**
     * Creates a new child Company ($command->parentId).
     *
     * @param \App\Command\Company\CreateNew $command
     *
     * @throws \App\Exception\Validate\CompanyException
     * @throws \App\Exception\Create\CompanyException
     *
     * @return \App\Entity\Company
     */
    public function handleCreateNew(CreateNew $command) : CompanyEntity {
        try {
            $this->validator->assertLongString($command->name, 'name');
            $this->validator->assertNullableId($command->parentId, 'parentId');
            $this->validator->assertIdentity($command->identity, 'identity');
        } catch (ValidationException $e) {
            throw new Validate\CompanyException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        $company = $this->repository->create(
            [
                'name'       => $command->name,
                'parent_id'  => $command->parentId,
                'created_at' => time()
            ]
        );

        $company->public_key  = Key::createNewRandomKey()->saveToAsciiSafeString();
        $company->private_key = Key::createNewRandomKey()->saveToAsciiSafeString();

        try {
            $company = $this->repository->saveNewCompany($company, $command->identity);
            $event   = $this->eventFactory->create('Company\\Created', $company, $command->identity);
            $this->emitter->emit($event);
        } catch (\Exception $e) {
            throw new Create\CompanyException('Error while trying to create a company', 500, $e);
        }

        return $company;
    }

    /**
     * ize a Company.
     *
     * @param \App\Command\Company\Setup $command
     *
     * @throws \App\Exception\Validate\CompanyException
     * @throws \App\Exception\Create\CompanyException
     *
     * @return \Illuminate\Support\Collection of handlers
     */
    public function handleSetup(Setup $command) : Collection {
        try {
            $this->validator->assertId($command->companyId, 'companyId');
            $this->validator->assertIdentity($command->identity, 'identity');
        } catch (ValidationException $e) {
            throw new Validate\CompanyException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        try {
            $company = $this->repository->find($command->companyId);

            if ($company->parentId) {
                $handlerServices = $this->handlerServiceRepository->getByServiceCompanyId($company->parentId);
            } else {
                $handlerServices = $this->handlerServiceRepository->getAll();
            }

            // populate company services
            foreach ($handlerServices as $handlerService) {
                if ($handlerService->privacy === HandlerService::PRIVACY_PRIVATE) {
                    continue;
                }

                $service = $this->serviceRepository->create(
                    [
                    'company_id'         => $command->companyId,
                    'handler_service_id' => $handlerService->id,
                    'listens'            => $handlerService->listens
                    ]
                );
                $this->serviceRepository->upsert($service);
            }

            $event = $this->eventFactory->create('Company\\Setup', $company, $command->identity);
            $this->emitter->emit($event);

            return $handlerServices;
        } catch (\Exception $e) {
            throw new Create\CompanyException('Error while trying to setup new company', 500, $e);
        }
    }

    /**
     * Updates a Company.
     *
     * @param \App\Command\Company\UpdateOne $command
     *
     * @throws \App\Exception\Validate\CompanyException
     * @throws \App\Exception\Update\CompanyException
     *
     * @return \App\Entity\Company
     */
    public function handleUpdateOne(UpdateOne $command) : CompanyEntity {
        try {
            $this->validator->assertCompany($command->company, 'company');
            $this->validator->assertId($command->company->id, 'id');
            $this->validator->assertMediumString($command->name, 'name');
            $this->validator->assertIdentity($command->identity, 'identity');
        } catch (ValidationException $e) {
            throw new Validate\CompanyException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        $company            = $command->company;
        $company->name      = $command->name;
        $company->updatedAt = time();

        try {
            $company = $this->repository->save($company);
            $event   = $this->eventFactory->create('Company\\Updated', $company, $command->identity);
            $this->emitter->emit($event);
        } catch (\Exception $e) {
            throw new Update\CompanyException('Error while trying to update a company', 500, $e);
        }

        return $company;
    }

    /**
     * Deletes a Company.
     *
     * @param \App\Command\Company\DeleteOne $command
     *
     * @throws \App\Exception\Validate\CompanyException
     * @throws \App\Exception\NotFound\CompanyException
     *
     * @return void
     */
    public function handleDeleteOne(DeleteOne $command) {
        try {
            $this->validator->assertCompany($command->company, 'company');
            $this->validator->assertId($command->company->id, 'id');
            $this->validator->assertIdentity($command->identity, 'identity');
        } catch (ValidationException $e) {
            throw new Validate\CompanyException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        $rowsAffected = $this->repository->delete($command->company->id);

        if (! $rowsAffected) {
            throw new NotFound\CompanyException('No companies found for deletion', 404);
        }

        $event = $this->eventFactory->create('Company\\Deleted', $command->company, $command->identity);
        $this->emitter->emit($event);
    }
}
