<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Handler;

use App\Command\Credential\CreateNew;
use App\Command\Credential\DeleteOne;
use App\Command\Credential\UpdateOne;
use App\Entity\Credential as CredentialEntity;
use App\Event\Credential\Created;
use App\Event\Credential\Deleted;
use App\Event\Credential\Updated;
use App\Exception\Create;
use App\Exception\NotFound;
use App\Exception\Update;
use App\Exception\Validate;
use App\Repository\CredentialInterface;
use App\Validator\Credential as CredentialValidator;
use Defuse\Crypto\Key;
use Interop\Container\ContainerInterface;
use League\Event\Emitter;
use Respect\Validation\Exceptions\ValidationException;

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
        $container[self::class] = function (ContainerInterface $container) : HandlerInterface {
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
     * @throws App\Exception\Validate\CredentialException
     * @throws App\Exception\Create\CredentialException
     *
     * @return App\Entity\Credential
     */
    public function handleCreateNew(CreateNew $command) : CredentialEntity {
        try {
            $this->validator->assertName($command->name);
            $this->validator->assertFlag($command->production);
            $this->validator->assertId($command->company->id);
        } catch (ValidationException $e) {
            throw new Validate\CredentialException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        $credential = $this->repository->create(
            [
                'name'       => $command->name,
                'production' => $this->validator->validateFlag($command->production),
                'company_id' => $command->company->id,
                'created_at' => time()
            ]
        );

        $credential->public  = md5((string) time()); // Key::createNewRandomKey()->saveToAsciiSafeString();
        $credential->private = md5((string) time()); // Key::createNewRandomKey()->saveToAsciiSafeString();

        try {
            $credential = $this->repository->save($credential);
            $event      = new Created($credential, $command->identity);
            $this->emitter->emit($event);
        } catch (\Exception $e) {
            throw new Create\CredentialException('Error while trying to create a credential', 500, $e);
        }

        return $credential;
    }

    /**
     * Updates a Credential.
     *
     * @param App\Command\Credential\UpdateOne $command
     *
     * @throws App\Exeption\Validate\CredentialException
     * @throws App\Exception\Update\CredentialException
     *
     * @return App\Entity\Credential
     */
    public function handleUpdateOne(UpdateOne $command) : CredentialEntity {
        try {
            $this->validator->assertId($command->credentialId);
            $this->validator->assertName($command->name);
        } catch (ValidationException $e) {
            throw new Validate\CredentialException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        $credential            = $this->repository->find($command->credentialId);
        $credential->name      = $command->name;
        $credential->updatedAt = time();

        try {
            $credential = $this->repository->save($credential);
            $event      = new Updated($credential, $command->identity);
            $this->emitter->emit($event);
        } catch (\Exception $e) {
            throw new Update\CredentialException('Error while trying to update a credential', 500, $e);
        }

        return $credential;
    }

    /**
     * Deletes a Credential.
     *
     * @param App\Command\Credential\DeleteOne $command
     *
     * @throws App\Exception\Validate\CredentialException
     * @throws App\Exception\NotFound\CredentialException
     *
     * @return void
     */
    public function handleDeleteOne(DeleteOne $command) {
        try {
            $this->validator->assertId($command->credential->id);
        } catch (ValidationException $e) {
            throw new Validate\CredentialException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        $credential   = $this->repository->find($command->credential->id);
        $rowsAffected = $this->repository->delete($command->credential->id);

        if (! $rowsAffected) {
            throw new NotFound\CredentialException('No credentials found for deletion', 404);
        }

        $event = new Deleted($credential, $command->identity);
        $this->emitter->emit($event);
    }
}
