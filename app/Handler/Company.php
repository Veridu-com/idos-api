<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Handler;

use App\Command\Company\CreateNew;
use App\Command\Company\DeleteOne;
use App\Command\Company\UpdateOne;
use App\Entity\Company as CompanyEntity;
use App\Event\Company\Created;
use App\Event\Company\Deleted;
use App\Event\Company\Updated;
use App\Exception\Create;
use App\Exception\NotFound;
use App\Exception\Update;
use App\Exception\Validate;
use App\Repository\CompanyInterface;
use App\Validator\Company as CompanyValidator;
use Defuse\Crypto\Key;
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
            $this->validator->assertLongString($command->name);
            $this->validator->assertParentId($command->parentId);
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

        $company->public_key  = md5((string) time()); //Key::createNewRandomKey()->saveToAsciiSafeString();
        $company->private_key = md5((string) time()); //Key::createNewRandomKey()->saveToAsciiSafeString();

        try {
            $company = $this->repository->saveNewCompany($company, $command->identity);
            $event   = new Created($company, $command->identity);
            $this->emitter->emit($event);
        } catch (\Exception $e) {
            throw new Create\CompanyException('Error while trying to create a company', 500, $e);
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
        try {
            $this->validator->assertId($command->company->id);
            $this->validator->assertMediumLatinName($command->name);
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
            $event   = new Updated($company, $command->identity);
            $this->emitter->emit($event);
        } catch (\Exception $e) {
            throw new Update\CompanyException('Error while trying to update a company', 500, $e);
        }

        return $company;
    }

    /**
     * Deletes a Company.
     *
     * @param App\Command\Company\DeleteOne $command
     *
     * @return void
     */
    public function handleDeleteOne(DeleteOne $command) {
        try {
            $this->validator->assertCompany($command->company);
            $this->validator->assertId($command->company->id);
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

        $event = new Deleted($command->company, $command->identity);
        $this->emitter->emit($event);
    }
}
