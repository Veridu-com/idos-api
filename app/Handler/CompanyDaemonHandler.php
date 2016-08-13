<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types=1);

namespace App\Handler;

use App\Command\CompanyDaemonHandler\CreateNew;
use App\Command\CompanyDaemonHandler\DeleteAll;
use App\Command\CompanyDaemonHandler\DeleteOne;
use App\Entity\CompanyDaemonHandler as CompanyDaemonHandlerEntity;
use App\Exception\NotFound;
use App\Repository\CompanyDaemonHandlerInterface;
use App\Validator\CompanyDaemonHandler as CompanyDaemonHandlerValidator;
use Interop\Container\ContainerInterface;

/**
 * Handles CompanyDaemonHandler commands.
 */
class CompanyDaemonHandler implements HandlerInterface {
    /**
     * CompanyDaemonHandler Repository instance.
     *
     * @var App\Repository\CompanyDaemonHandlerInterface
     */
    protected $repository;
    /**
     * CompanyDaemonHandler Validator instance.
     *
     * @var App\Validator\CompanyDaemonHandler
     */
    protected $validator;

    /**
     * {@inheritdoc}
     */
    public static function register(ContainerInterface $container) {
        $container[self::class] = function (ContainerInterface $container) {
            return new \App\Handler\CompanyDaemonHandler(
                $container
                    ->get('repositoryFactory')
                    ->create('CompanyDaemonHandler'),
                $container
                    ->get('validatorFactory')
                    ->create('CompanyDaemonHandler')
            );
        };
    }

    /**
     * Class constructor.
     *
     * @param App\Repository\CompanyDaemonHandlerInterface $repository
     * @param App\Validator\CompanyDaemonHandler           $validator
     *
     * @return void
     */
    public function __construct(
        CompanyDaemonHandlerInterface $repository,
        CompanyDaemonHandlerValidator $validator
    ) {
        $this->repository = $repository;
        $this->validator  = $validator;
    }

    /**
     * Creates a new CompanyDaemonHandler.
     *
     * @param App\Command\CompanyDaemonHandler\CreateNew $command
     *
     * @return App\Entity\CompanyDaemonHandler
     */
    public function handleCreateNew(CreateNew $command) : CompanyDaemonHandlerEntity {
        $this->validator->assertId($command->decodedDaemonHandlerId);

        $now    = time();
        $entity = $this->repository->create(
            [
                'company_id'            => $command->companyId,
                'daemon_handler_id'    => $command->companyId,
                'created_at'            => $now,
                'updated_at'            => $now
            ]
        );

        $entity = $this->repository->save($entity);
        $entity = $this->repository->findOne($entity->id, $entity->companyId);

        return $entity;
    }

    /**
     * Deletes all daemon handlers ($command->companyId).
     *
     * @param App\Command\CompanyDaemonHandler\DeleteAll $command
     *
     * @return int
     */
    public function handleDeleteAll(DeleteAll $command) : int {
        $this->validator->assertId($command->companyId);

        return $this->repository->deleteByCompanyId($command->companyId);
    }

    /**
     * Deletes a CompanyDaemonHandler.
     *
     * @param App\Command\CompanyDaemonHandler\DeleteOne $command
     *
     * @throws App\Exception\NotFound
     * 
     * @return int
     */
    public function handleDeleteOne(DeleteOne $command) : int {
        $this->validator->assertId($command->id);
        $this->validator->assertId($command->companyId);

        $rowsAffected = $this->repository->deleteOne($command->id, $command->companyId);

        if (! $rowsAffected) {
            throw new NotFound();
        }

        return $rowsAffected;
    }
}
