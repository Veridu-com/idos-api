<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types=1);

namespace App\Handler;

use App\Command\Service\CreateNew;
use App\Command\Service\DeleteAll;
use App\Command\Service\DeleteOne;
use App\Command\Service\UpdateOne;
use App\Entity\Service as ServiceEntity;
use App\Exception\NotAllowed;
use App\Exception\NotFound;
use App\Repository\ServiceInterface;
use App\Validator\Service as ServiceValidator;
use Interop\Container\ContainerInterface;

/**
 * Handles Service commands.
 */
class Service implements HandlerInterface {
    /**
     * Service Repository instance.
     *
     * @var App\Repository\ServiceInterface
     */
    protected $repository;
    /**
     * Service Validator instance.
     *
     * @var App\Validator\Service
     */
    protected $validator;

    /**
     * {@inheritdoc}
     */
    public static function register(ContainerInterface $container) {
        $container[self::class] = function (ContainerInterface $container) {
            return new \App\Handler\Service(
                $container
                    ->get('repositoryFactory')
                    ->create('Service'),
                $container
                    ->get('validatorFactory')
                    ->create('Service')
            );
        };
    }

    /**
     * Class constructor.
     *
     * @param App\Repository\ServiceInterface $repository
     * @param App\Validator\Service           $validator
     *
     * @return void
     */
    public function __construct(
        ServiceInterface $repository,
        ServiceValidator $validator
    ) {
        $this->repository = $repository;
        $this->validator  = $validator;
    }

    /**
     * Creates a new Service.
     *
     * @param App\Command\Service\CreateNew $command
     *
     * @return App\Entity\Service
     */
    public function handleCreateNew(CreateNew $command) : ServiceEntity {
        $this->validator->assertCompany($command->company);

        $input = [ 'company_id' => $command->company->id ];

        // required params
        $this->validator->assertName($command->name);
        $input['name'] = $command->name;

        $this->validator->assertUrl($command->url);
        $input['url'] = $command->url;
        
        $this->validator->assertAuthUsername($command->authUsername);
        $input['auth_username'] = $command->authUsername;

        $this->validator->assertAuthPassword($command->authPassword);
        $input['auth_password'] = $command->authPassword;

        // optional params
        if ($command->listens) {
            $this->validator->assertListens($command->listens);
            $input['listens'] = $command->listens;
        }
        if ($command->triggers) {
            $this->validator->assertTriggers($command->triggers);
            $input['triggers'] = $command->triggers;
        }
        if ($command->access !== null) {
            $this->validator->assertAccess($command->access);
            $input['access'] = $command->access;
        }
        if ($command->enabled !== null) {
            $this->validator->assertEnabled($command->enabled);
            $input['enabled'] = $command->enabled;
        }

        $input['created_at'] = time();

        $entity = $this->repository->create($input);

        return $this->repository->save($entity);
    }

    /**
     * Updates a Service.
     *
     * @param App\Command\Service\UpdateOne $command
     *
     * @return App\Entity\Service
     */
    public function handleUpdateOne(UpdateOne $command) : ServiceEntity {
        $this->validator->assertCompany($command->company);
        $this->validator->assertId($command->serviceId);

        $input = [];
        if ($command->name) {
            $this->validator->assertName($command->name);
            $input['name'] = $command->name;
        }
        if ($command->listens) {
            $this->validator->assertListens($command->listens);
            $input['listens'] = $command->listens;
        }
        if ($command->triggers) {
            $this->validator->assertTriggers($command->triggers);
            $input['triggers'] = $command->triggers;
        }
        if ($command->url) {
            $this->validator->assertUrl($command->url);
            $input['url'] = $command->url;
        }
        if ($command->access !== null) {
            $this->validator->assertAccess($command->access);
            $input['access'] = $command->access;
        }
        if ($command->enabled !== null) {
            $this->validator->assertEnabled($command->enabled);
            $input['enabled'] = $command->enabled;
        }
        if ($command->authUsername) {
            $this->validator->assertAuthUsername($command->authUsername);
            $input['auth_username'] = $command->authUsername;
        }
        if ($command->authPassword) {
            $this->validator->assertAuthPassword($command->authPassword);
            $input['auth_password'] = $command->authPassword;
        }

        $entity = $this->repository->findOne($command->serviceId, $command->company);

        // Any thoughts on a better place of verifying this
        if ($command->company->id != $entity->companyId) {
            throw new NotAllowed;
        }

        foreach ($input as $key => $value) {
            $entity->$key = $value;
        }

        $success = $this->repository->save($entity);


        return $entity;
    }

    /**
     * Deletes all service handlers ($command->companyId).
     *
     * @param App\Command\Service\DeleteAll $command
     *
     * @return int
     */
    public function handleDeleteAll(DeleteAll $command) : int {
        $this->validator->assertCompany($command->company);

        return $this->repository->deleteByCompanyId($command->company->id);
    }

    /**
     * Deletes a Service.
     *
     * @param App\Command\Service\DeleteOne $command
     *
     * @throws App\Exception\NotFound
     *
     * @return int
     */
    public function handleDeleteOne(DeleteOne $command) : int {
        $this->validator->assertCompany($command->company);
        $this->validator->assertId($command->serviceId);

        $rowsAffected = $this->repository->deleteOne($command->serviceId, $command->company);

        return $rowsAffected;
    }
}
