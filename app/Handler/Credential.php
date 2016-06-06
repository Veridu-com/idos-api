<?php
/**
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace App\Handler;

use App\Command\CreateCredential;
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
     * @param App\Command\CreateCredential $command
     *
     * @return array
     */
    public function handleCreateCredential(CreateCredential $command) {
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
}
