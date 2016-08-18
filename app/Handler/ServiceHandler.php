<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types=1);

namespace App\Handler;

use App\Command\ServiceHandler\CreateNew;
use App\Command\ServiceHandler\DeleteAll;
use App\Command\ServiceHandler\DeleteOne;
use App\Command\ServiceHandler\UpdateOne;
use App\Entity\ServiceHandler as ServiceHandlerEntity;
use App\Exception\NotFound;
use App\Repository\ServiceHandlerInterface;
use App\Validator\ServiceHandler as ServiceHandlerValidator;
use Interop\Container\ContainerInterface;

/**
 * Handles ServiceHandler commands.
 */
class ServiceHandler implements HandlerInterface {
    /**
     * ServiceHandler Repository instance.
     *
     * @var App\Repository\ServiceHandlerInterface
     */
    protected $repository;
    /**
     * ServiceHandler Validator instance.
     *
     * @var App\Validator\ServiceHandler
     */
    protected $validator;

    /**
     * {@inheritdoc}
     */
    public static function register(ContainerInterface $container) {
        $container[self::class] = function (ContainerInterface $container) {
            return new \App\Handler\ServiceHandler(
                $container
                    ->get('repositoryFactory')
                    ->create('ServiceHandler'),
                $container
                    ->get('validatorFactory')
                    ->create('ServiceHandler')
            );
        };
    }

    /**
     * Class constructor.
     *
     * @param App\Repository\ServiceHandlerInterface $repository
     * @param App\Validator\ServiceHandler           $validator
     *
     * @return void
     */
    public function __construct(
        ServiceHandlerInterface $repository,
        ServiceHandlerValidator $validator
    ) {
        $this->repository = $repository;
        $this->validator  = $validator;
    }

    /**
     * Creates a new ServiceHandler.
     *
     * @param App\Command\ServiceHandler\CreateNew $command
     *
     * @return App\Entity\ServiceHandler
     */
    public function handleCreateNew(CreateNew $command) : ServiceHandlerEntity {
        $this->validator->assertName($command->name);
        $this->validator->assertSource($command->source);
        $this->validator->assertId($command->companyId);
        $this->validator->assertSlug($command->serviceSlug);
        $this->validator->assertAuthPassword($command->authPassword);
        $this->validator->assertAuthUsername($command->authUsername);
        $this->validator->assertLocation($command->location);

        $now    = time();
        $entity = $this->repository->create(
            [
                'company_id'   => $command->companyId,
                'service_slug' => $command->serviceSlug,
                'name'         => $command->name,
                'source'       => $command->source,
                'location'     => $command->location,
                'authPassword' => $command->authPassword,
                'authUsername' => $command->authUsername,
                'created_at'   => $now,
                'updated_at'   => $now
            ]
        );

        $this->repository->save($entity);

        return $entity;
    }

    /**
     * Updates a ServiceHandler.
     *
     * @param App\Command\ServiceHandler\UpdateOne $command
     *
     * @return App\Entity\ServiceHandler
     */
    public function handleUpdateOne(UpdateOne $command) : ServiceHandlerEntity {
        $this->validator->assertSlug($command->slug);
        $this->validator->assertSlug($command->serviceSlug);
        $this->validator->assertId($command->companyId);

        // optional inputs
        if ($command->name) {
            $this->validator->assertName($command->name);
            $input['name'] = $command->name;
        }
        if ($command->location) {
            $this->validator->assertName($command->location);
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

        $entity = $this->repository->findOne($command->companyId, $command->slug, $command->serviceSlug);

        // fills entity
        // @FIXME: There could exist on AbstractEntity to fill it based on a [ key => value ] array.
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
     * Deletes all service handlers ($command->companyId).
     *
     * @param App\Command\ServiceHandler\DeleteAll $command
     *
     * @return int
     */
    public function handleDeleteAll(DeleteAll $command) : int {
        $this->validator->assertId($command->companyId);

        return $this->repository->deleteByCompanyId($command->companyId);
    }

    /**
     * Deletes a ServiceHandler.
     *
     * @param App\Command\ServiceHandler\DeleteOne $command
     *
     * @throws App\Exception\NotFound
     *
     * @return int
     */
    public function handleDeleteOne(DeleteOne $command) : int {
        $this->validator->assertId($command->companyId);
        $this->validator->assertSlug($command->slug);
        $this->validator->assertSlug($command->serviceSlug);

        $rowsAffected = $this->repository->deleteOne($command->companyId, $command->slug, $command->serviceSlug);

        if (! $rowsAffected) {
            throw new NotFound();
        }

        return $rowsAffected;
    }
}
