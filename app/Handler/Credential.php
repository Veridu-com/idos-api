<?php
/**
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace App\Handler;

use App\Command\Credential\CreateNew;
use App\Command\Credential\DeleteAll;
use App\Command\Credential\DeleteOne;
use App\Command\Credential\UpdateOne;
use App\Repository\CredentialInterface;
use App\Validator\Credential as CredentialValidator;
use Defuse\Crypto\Key;
use Interop\Container\ContainerInterface;

/**
 * Handles Credential commands.
 */
class Credential implements HandlerInterface {
    /**
     * Credential Repository instance.
     *
     * @var App\Repository\CredentialInterface
     */
    protected $repository;
    /**
     * Credential Validator instance.
     *
     * @var App\Validator\Credential
     */
    protected $validator;

    /**
     * {@inheritDoc}
     */
    public static function register(ContainerInterface $container) {
        $container[self::class] = function (ContainerInterface $container) {
            return new \App\Handler\Credential(
                $container
                    ->get('repositoryFactory')
                    ->create('Credential'),
                $container
                    ->get('validatorFactory')
                    ->create('Credential')
            );
        };
    }

    /**
     * Class constructor.
     *
     * @param App\Repository\CredentialInterface
     * @param App\Validator\Credential
     *
     * @return void
     */
    public function __construct(
        CredentialInterface $repository,
        CredentialValidator $validator
    ) {
        $this->repository = $repository;
        $this->validator  = $validator;
    }

    /**
     * Creates a new child Credential ($command->companyId).
     *
     * @param App\Command\Credential\CreateNew $command
     *
     * @return array
     */
    public function handleCreateNew(CreateNew $command) {
        $this->validator->assertName($command->name);
        $this->validator->assertProduction($command->production);
        $this->validator->assertCompanyId($command->companyId);

        $credential = $this->repository->create(
            [
                'name'       => $command->name,
                'production' => $this->validator->productionValue($command->production),
                'company_id' => $command->companyId
            ]
        );

        $credential->public  = Key::createNewRandomKey()->saveToAsciiSafeString();
        $credential->private = Key::createNewRandomKey()->saveToAsciiSafeString();

        $credential->saveOrFail();

        return $credential->toArray();
    }

    /**
     * Updates a Credential.
     *
     * @param App\Command\CredentialUpdateOne $command
     *
     * @return array
     */
    public function handleCredentialUpdateOne(CredentialUpdateOne $command) {
        $this->validator->assertId($command->credentialId);
        $this->validator->assertName($command->newName);

        $credential       = $this->repository->findById($command->credentialId);
        $credential->name = $command->newName;

        $this->repository->save($credential);

        return $credential->toArray();
    }

    /**
     * Deletes a Credential.
     *
     * @param App\Command\CredentialDeleteOne $command
     *
     * @return void
     */
    public function handleCredentialDeleteOne(CredentialDeleteOne $command) {
        $this->validator->assertId($command->credentialId);

        $this->repository->deleteById($command->credentialId);
    }

    /**
     * Deletes all credentials ($command->companyId)
     *
     * @param App\Command\CredentialDeleteAll $command
     *
     * @return void
     */
    public function handleCredentialDeleteAll(CredentialDeleteAll $command) {
        $this->validator->assertId($command->companyId);

        $this->repository->deleteByCompanyId($command->companyId);
    }
}
