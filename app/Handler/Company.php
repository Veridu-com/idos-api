<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Handler;

use App\Command\Company\CreateNew;
use App\Command\Company\DeleteAll;
use App\Command\Company\DeleteOne;
use App\Command\Company\UpdateOne;
use App\Entity\Company as CompanyEntity;
use App\Entity\Role;
use App\Event\Company\Created;
use App\Event\Company\Deleted;
use App\Event\Company\DeletedMulti;
use App\Event\Company\Updated;
use App\Exception\AppException;
use App\Exception\NotFound;
use App\Repository\CompanyInterface;
use App\Validator\Company as CompanyValidator;
use Defuse\Crypto\Key;
use Interop\Container\ContainerInterface;
use League\Event\Emitter;

/**
 * Handles Company commands.
 */
class Company implements HandlerInterface {
    /**
     * Company Repository instance.
     *
     * @var App\Repository\CompanyInterface
     */
    protected $repository;
    /**
     * Company Validator instance.
     *
     * @var App\Validator\Company
     */
    protected $validator;
    /**
     * Event emitter instance.
     *
     * @var \League\Event\Emitter
     */
    protected $emitter;

    /**
     * {@inheritdoc}
     */
    public static function register(ContainerInterface $container) {
        $container[self::class] = function (ContainerInterface $container) : HandlerInterface {
            return new \App\Handler\Company(
                $container
                    ->get('repositoryFactory')
                    ->create('Company'),
                $container
                    ->get('validatorFactory')
                    ->create('Company'),
                $container
                    ->get('eventEmitter')
            );
        };
    }

    /**
     * Class constructor.
     *
     * @param App\Repository\CompanyInterface $repository
     * @param App\Validator\Company           $validator
     * @param \League\Event\Emitter           $emitter
     *
     * @return void
     */
    public function __construct(
        CompanyInterface $repository,
        CompanyValidator $validator,
        Emitter $emitter
    ) {
        $this->repository = $repository;
        $this->validator  = $validator;
        $this->emitter    = $emitter;
    }

    /**
     * Creates a new child Company ($command->parentId).
     *
     * @param App\Command\Company\CreateNew $command
     *
     * @return App\Entity\Company
     */
    public function handleCreateNew(CreateNew $command) : CompanyEntity {
        try {
            $this->validator->assertMediumLatinName($command->name);
            $this->validator->assertParentId($command->parentId);
        } catch (\Exception $exception) {
            // Respect\Validation\Exceptions\ExceptionInterface
            throw new AppException(
                sprintf(
                    'Invalid input: %s',
                    implode('; ', $exception->getMessages())
                ),
                400
            );
        }

        $company = $this->repository->create(
            [
                'name'       => $command->name,
                'parent_id'  => $command->parentId,
                'created_at' => time()
            ]
        );

        $company->public_key  = md5((string) time()); //Key::createNewRandomKey()->saveToAsciiSafeString();
        $company->private_key = md5((string) time()); //Key::createNewRandomKey()->saveToAsciiSafeString();

        

        try {
            $company = $this->repository->saveNewCompany($company, $command->identity);
            $event   = new Created($company, $command->identity);
            $this->emitter->emit($event);
        } catch (\Exception $exception) {
            throw new AppException('Error while creating a company');
        }

        return $company;
    }

    /**
     * Updates a Company.
     *
     * @param App\Command\Company\UpdateOne $command
     *
     * @return App\Entity\Company
     */
    public function handleUpdateOne(UpdateOne $command) : CompanyEntity {
        $this->validator->assertCompany($command->company);
        $this->validator->assertIdentity($command->identity);
        $this->validator->assertMediumLatinName($command->name);

        $company = $command->company;
        $company->name      = $command->name;
        $company->updatedAt = time();

        try {
            $company = $this->repository->save($company);
            $event   = new Updated($company, $command->identity);
            $this->emitter->emit($event);
        } catch (\Exception $exception) {
            throw new AppException('Error while updating a company id ' . $command->companyId);
        }

        return $company;
    }

    /**
     * Deletes a Company.
     *
     * @param App\Command\Company\DeleteOne $command
     *
     * @return int
     */
    public function handleDeleteOne(DeleteOne $command) : int {
        $this->validator->assertCompany($command->company);
        $this->validator->assertIdentity($command->identity);
        $this->validator->assertId($command->company->id);

        $rowsAffected = $this->repository->delete($command->company->id);

        if ($rowsAffected) {
            $event = new Deleted($command->company, $command->identity);
            $this->emitter->emit($event);
        } else {
            throw new NotFound();
        }

        return $rowsAffected;
    }

    /**
     * Deletes all child Company ($command->parentId).
     *
     * @param App\Command\Company\DeleteAll $command
     *
     * @return int
     */
    public function handleDeleteAll(DeleteAll $command) : int {
        $this->validator->assertId($command->parentId);

        $deletedCompanies = $this->repository->getAllByParentId($command->parentId);

        $rowsAffected = $this->repository->deleteByParentId($command->parentId);

        $event = new DeletedMulti($deletedCompanies);
        $this->emitter->emit($event);

        return $rowsAffected;
    }
}
