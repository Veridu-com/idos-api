<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Handler;

use App\Command\Credential\CreateNew;
use App\Command\Credential\DeleteAll;
use App\Command\Credential\DeleteOne;
use App\Command\Credential\UpdateOne;
use App\Entity\Credential as CredentialEntity;
use App\Event\Credential\Created;
use App\Event\Credential\Deleted;
use App\Event\Credential\DeletedMulti;
use App\Event\Credential\Updated;
use App\Exception\AppException;
use App\Exception\NotFound;
use App\Repository\CredentialInterface;
use App\Validator\Credential as CredentialValidator;
use Defuse\Crypto\Key;
use Interop\Container\ContainerInterface;
use League\Event\Emitter;

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
     * Event emitter instance.
     *
     * @var League\Event\Emitter
     */
    protected $emitter;

    /**
     * {@inheritdoc}
     */
    public static function register(ContainerInterface $container) {
        $container[self::class] = function (ContainerInterface $container) {
            return new \App\Handler\Credential(
                $container
                    ->get('repositoryFactory')
                    ->create('Credential'),
                $container
                    ->get('validatorFactory')
                    ->create('Credential'),
                $container
                    ->get('eventEmitter')
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
        CredentialValidator $validator,
        Emitter $emitter
    ) {
        $this->repository = $repository;
        $this->validator  = $validator;
        $this->emitter    = $emitter;
    }

    /**
     * Creates a new child Credential ($command->companyId).
     *
     * @param App\Command\Credential\CreateNew $command
     *
     * @return App\Entity\Credential
     */
    public function handleCreateNew(CreateNew $command) : CredentialEntity {
        $this->validator->assertName($command->name);
        $this->validator->assertProduction($command->production);
        $this->validator->assertCompanyId($command->companyId);

        $credential = $this->repository->create(
            [
                'name'       => $command->name,
                'production' => $this->validator->productionValue($command->production),
                'company_id' => $command->companyId,
                'created_at' => time()
            ]
        );

        $credential->public  = md5((string) time()); // Key::createNewRandomKey()->saveToAsciiSafeString();
        $credential->private = md5((string) time()); // Key::createNewRandomKey()->saveToAsciiSafeString();

        try {
            $credential = $this->repository->save($credential);
            $event      = new Created($credential);
            $this->emitter->emit($event);
        }
        catch(\Exception $exception) {
            throw new AppException('Error while creating a credential');
        }

        return $credential;
    }

    /**
     * Updates a Credential.
     *
     * @param App\Command\Credential\UpdateOne $command
     *
     * @return App\Entity\Credential
     */
    public function handleUpdateOne(UpdateOne $command) : CredentialEntity {
        $this->validator->assertId($command->credentialId);
        $this->validator->assertName($command->name);

        $credential            = $this->repository->find($command->credentialId);
        $credential->name      = $command->name;
        $credential->updatedAt = time();

        try {
            $credential = $this->repository->save($credential);
            $event      = new Updated($credential);
            $this->emitter->emit($event);
        }
        catch(\Exception $exception) {
            throw new AppException('Error while updating a credential id' . $command->credentialId);
        }

        return $credential;
    }

    /**
     * Deletes a Credential.
     *
     * @param App\Command\Credential\DeleteOne $command
     *
     * @return int
     */
    public function handleDeleteOne(DeleteOne $command) : int {
        $this->validator->assertId($command->credentialId);

        $credential = $this->repository->find($command->credentialId);

        $rowsAffected = $this->repository->delete($command->credentialId);

        if ($rowsAffected) {
            $event = new Deleted($credential);
            $this->emitter->emit($event);
        } else {
            throw new \NotFound();
        }

        return $rowsAffected;
    }

    /**
     * Deletes all credentials ($command->companyId).
     *
     * @param App\Command\Credential\DeleteAll $command
     *
     * @return int
     */
    public function handleDeleteAll(DeleteAll $command) : int {
        $this->validator->assertId($command->companyId);

        $credentials = $this->repository->findByCompanyId($command->companyId);

        $rowsAffected = $this->repository->deleteByCompanyId($command->companyId);

        $event = new DeletedMulti($credentials);
        $this->emitter->emit($event);

        return $rowsAffected;
    }
}
