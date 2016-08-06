<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types=1);

namespace App\Handler;

use App\Command\CompanyServiceHandler\CreateNew;
use App\Command\CompanyServiceHandler\DeleteAll;
use App\Command\CompanyServiceHandler\DeleteOne;
use App\Command\CompanyServiceHandler\UpdateOne;
use App\Entity\CompanyServiceHandler as CompanyServiceHandlerEntity;
use App\Exception\NotFound;
use App\Repository\CompanyServiceHandlerInterface;
use App\Validator\CompanyServiceHandler as CompanyServiceHandlerValidator;
use Interop\Container\ContainerInterface;

/**
 * Handles CompanyServiceHandler commands.
 */
class CompanyServiceHandler implements HandlerInterface {
    /**
     * CompanyServiceHandler Repository instance.
     *
     * @var App\Repository\CompanyServiceHandlerInterface
     */
    protected $repository;
    /**
     * CompanyServiceHandler Validator instance.
     *
     * @var App\Validator\CompanyServiceHandler
     */
    protected $validator;

    /**
     * {@inheritdoc}
     */
    public static function register(ContainerInterface $container) {
        $container[self::class] = function (ContainerInterface $container) {
            return new \App\Handler\CompanyServiceHandler(
                $container
                    ->get('repositoryFactory')
                    ->create('CompanyServiceHandler'),
                $container
                    ->get('validatorFactory')
                    ->create('CompanyServiceHandler')
            );
        };
    }

    /**
     * Class constructor.
     *
     * @param App\Repository\CompanyServiceHandlerInterface $repository
     * @param App\Validator\CompanyServiceHandler           $validator
     *
     * @return void
     */
    public function __construct(
        CompanyServiceHandlerInterface $repository,
        CompanyServiceHandlerValidator $validator
    ) {
        $this->repository = $repository;
        $this->validator  = $validator;
    }

    /**
     * Creates a new CompanyServiceHandler.
     *
     * @param App\Command\CompanyServiceHandler\CreateNew $command
     *
     * @return App\Entity\CompanyServiceHandler
     */
    public function handleCreateNew(CreateNew $command) : CompanyServiceHandlerEntity {
        $this->validator->assertId($command->serviceHandlerId);

        $now    = time();
        $entity = $this->repository->create(
            [
                'company_id'            => $command->companyId,
                'service_handler_id'    => $command->companyId,
                'created_at'            => $now,
                'updated_at'            => $now
            ]
        );

        $entity = $this->repository->save($entity);
        $entity = $this->repository->findOne($entity->id, $entity->companyId);

        return $entity;
    }

    /**
     * Deletes all service handlers ($command->companyId).
     *
     * @param App\Command\CompanyServiceHandler\DeleteAll $command
     *
     * @return int
     */
    public function handleDeleteAll(DeleteAll $command) : int {
        $this->validator->assertId($command->companyId);

        return $this->repository->deleteByCompanyId($command->companyId);
    }

    /**
     * Deletes a CompanyServiceHandler.
     *
     * @param App\Command\CompanyServiceHandler\DeleteOne $command
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
