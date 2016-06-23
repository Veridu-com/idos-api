<?php
/**
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace App\Handler;

use App\Command\Company\CreateNew;
use App\Command\Company\DeleteAll;
use App\Command\Company\DeleteOne;
use App\Command\Company\UpdateOne;
use App\Repository\CompanyInterface;
use App\Validator\Company as CompanyValidator;
use Defuse\Crypto\Key;
use Interop\Container\ContainerInterface;

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
     * {@inheritdoc}
     */
    public static function register(ContainerInterface $container) {
        $container[self::class] = function (ContainerInterface $container) {
            return new \App\Handler\Company(
                $container
                    ->get('repositoryFactory')
                    ->create('Company'),
                $container
                    ->get('validatorFactory')
                    ->create('Company')
            );
        };
    }

    /**
     * Class constructor.
     *
     * @param App\Repository\CompanyInterface
     * @param App\Validator\Company
     *
     * @return void
     */
    public function __construct(
        CompanyInterface $repository,
        CompanyValidator $validator
    ) {
        $this->repository = $repository;
        $this->validator  = $validator;
    }

    /**
     * Creates a new child Company ($command->parentId).
     *
     * @param App\Command\Company\CreateNew $command
     *
     * @return array
     */
    public function handleCreateNew(CreateNew $command) {
        $this->validator->assertName($command->name);
        $this->validator->assertParentId($command->parentId);

        $company = $this->repository->create(
            [
                'name'      => $command->name,
                'parent_id' => $command->parentId
            ]
        );

        $company->public_key  = Key::createNewRandomKey()->saveToAsciiSafeString();
        $company->private_key = Key::createNewRandomKey()->saveToAsciiSafeString();

        $this->repository->save($company);

        return $company->toArray();
    }

    /**
     * Updates a Company.
     *
     * @param App\Command\Company\UpdateOne $command
     *
     * @return array
     */
    public function handleUpdateOne(UpdateOne $command) {
        $this->validator->assertId($command->companyId);
        $this->validator->assertName($command->newName);

        $company       = $this->repository->findById($command->companyId);
        $company->name = $command->newName;

        $this->repository->save($company);

        return $company->toArray();
    }

    /**
     * Deletes a Company.
     *
     * @param App\Command\Company\DeleteOne $command
     *
     * @return void
     */
    public function handleDeleteOne(DeleteOne $command) {
        $this->validator->assertId($command->companyId);

        $this->repository->deleteById($command->companyId);
    }

    /**
     * Deletes all child Company ($command->parentId).
     *
     * @param App\Command\Company\DeleteAll $command
     *
     * @return void
     */
    public function handleDeleteAll(DeleteAll $command) {
        $this->validator->assertId($command->parentId);

        $this->repository->deleteByParentId($command->parentId);
    }
}