<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types=1);

namespace App\Handler;

use App\Command\DaemonHandler\CreateNew;
use App\Command\DaemonHandler\DeleteAll;
use App\Command\DaemonHandler\DeleteOne;
use App\Command\DaemonHandler\UpdateOne;
use App\Command\DaemonHandler\DetachOne;
use App\Command\DaemonHandler\AttachOne;
use App\Entity\DaemonHandler as DaemonHandlerEntity;
use App\Exception\NotFound;
use App\Repository\DaemonHandlerInterface;
use App\Validator\DaemonHandler as DaemonHandlerValidator;
use Interop\Container\ContainerInterface;

/**
 * Handles DaemonHandler commands.
 */
class DaemonHandler implements HandlerInterface {
    /**
     * DaemonHandler Repository instance.
     *
     * @var App\Repository\DaemonHandlerInterface
     */
    protected $repository;
    /**
     * DaemonHandler Validator instance.
     *
     * @var App\Validator\DaemonHandler
     */
    protected $validator;

    /**
     * {@inheritdoc}
     */
    public static function register(ContainerInterface $container) {
        $container[self::class] = function (ContainerInterface $container) {
            return new \App\Handler\DaemonHandler(
                $container
                    ->get('repositoryFactory')
                    ->create('DaemonHandler'),
                $container
                    ->get('validatorFactory')
                    ->create('DaemonHandler')
            );
        };
    }

    /**
     * Class constructor.
     *
     * @param App\Repository\DaemonHandlerInterface $repository
     * @param App\Validator\DaemonHandler           $validator
     *
     * @return void
     */
    public function __construct(
        DaemonHandlerInterface $repository,
        DaemonHandlerValidator $validator
    ) {
        $this->repository = $repository;
        $this->validator  = $validator;
    }

    /**
     * Creates a new DaemonHandler.
     *
     * @param App\Command\DaemonHandler\CreateNew $command
     *
     * @return App\Entity\DaemonHandler
     */
    public function handleCreateNew(CreateNew $command) : DaemonHandlerEntity {
        $this->validator->assertName($command->name);
        $this->validator->assertSource($command->source);
        $this->validator->assertId($command->companyId);
        $this->validator->assertSlug($command->daemonSlug);
        $this->validator->assertLocation($command->location);
        $this->validator->assertRunLevel($command->runLevel);
        $this->validator->assertStep($command->step);
        $this->validator->assertAuthPassword($command->authPassword);
        $this->validator->assertAuthUsername($command->authUsername);
        
        $now    = time();
        $entity = $this->repository->create(
            [
                'company_id'    => $command->companyId,
                'daemon_slug'   => $command->daemonSlug,
                'runlevel'      => $command->runLevel,
                'name'          => $command->name,
                'step'          => $command->step,
                'source'        => $command->source,
                'location'      => $command->location,
                'authPassword'  => $command->authPassword,
                'authUsername'  => $command->authUsername,
                'created_at'    => $now,
                'updated_at'    => $now
            ]
        );

        $entity = $this->repository->save($entity);

        return $entity;
    }

    /**
     * Updates a DaemonHandler.
     *
     * @param App\Command\DaemonHandler\UpdateOne $command
     *
     * @return App\Entity\DaemonHandler
     */
    public function handleUpdateOne(UpdateOne $command) : DaemonHandlerEntity {
        $this->validator->assertId($command->daemonHandlerId);

        // optional inputs
        if ($command->name) {
            $this->validator->assertName($command->name);
            $input['name'] = $command->name;
        }
        if ($command->location) {
            $this->validator->assertLocation($command->location);
            $input['location'] = $command->location;
        }
        if ($command->source) {
            $this->validator->assertSource($command->source);
            $input['source'] = $command->source;
        }
        if ($command->authPassword) {
            $this->validator->assertAuthPassword($command->authPassword);
            $input['authPassword'] = $command->authPassword;
        }
        if ($command->authUsername) {
            $this->validator->assertAuthUsername($command->authUsername);
            $input['authUsername'] = $command->authUsername;
        }
        if ($command->daemonSlug) {
            $this->validator->assertSlug($command->daemonSlug);
            $input['daemonSlug'] = $command->daemonSlug;
        }

        $entity = $this->repository->find($command->daemonHandlerId);

        foreach ($input as $key => $value) {
            $entity->$key = $value;
        }

        $success = $this->repository->save($entity);

        if (! $success) {
            throw new \RuntimeException('Error updating entity.');
        }

        return $entity;
    }

    /**
     * Deletes all daemon handlers ($command->companyId).
     *
     * @param App\Command\DaemonHandler\DeleteAll $command
     *
     * @return int
     */
    public function handleDeleteAll(DeleteAll $command) : int {
        $this->validator->assertId($command->companyId);

        return $this->repository->deleteByCompanyId($command->companyId);
    }

    /**
     * Deletes a DaemonHandler.
     *
     * @param App\Command\DaemonHandler\DeleteOne $command
     *
     * @throws App\Exception\NotFound
     * 
     * @return int
     */
    public function handleDeleteOne(DeleteOne $command) : int {
        $this->validator->assertId($command->daemonHandlerId);

        $rowsAffected = $this->repository->delete($command->daemonHandlerId);

        if (! $rowsAffected) {
            throw new NotFound();
        }

        return $rowsAffected;
    }

    /**
     * Detaches a Daemon handler from a company.
     *
     * @param App\Command\DaemonHandler\DetachOne $command
     *
     * @throws App\Exception\NotFound
     * 
     * @return int
     */
    public function handleDetachOne(DetachOne $command) : int {
        $this->validator->assertId($command->daemonHandlerId);
        $this->validator->assertId($command->relationCompanyId);

        $rowsAffected = $this->repository->detach($command->relationCompanyId, $command->daemonHandlerId);

        if (! $rowsAffected) {
            throw new NotFound();
        }

        return $rowsAffected;
    }

    /**
     * Attaches a Daemon handler from a company.
     *
     * @param App\Command\DaemonHandler\AttachOne $command
     *
     * @throws App\Exception\NotFound
     * 
     * @return int
     */
    public function handleAttachOne(AttachOne $command) : int {
        $this->validator->assertId($command->daemonHandlerId);
        $this->validator->assertId($command->relationCompanyId);

        $rowsAffected = $this->repository->attach($command->relationCompanyId, $command->daemonHandlerId);

        if (! $rowsAffected) {
            throw new NotFound();
        }

        return $rowsAffected;
    }
}
