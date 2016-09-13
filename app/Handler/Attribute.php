<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Handler;

use App\Exception\AppException;
use App\Command\Attribute\CreateNew;
use App\Command\Attribute\DeleteAll;
use App\Command\Attribute\DeleteOne;
use App\Command\Attribute\UpdateOne;
use App\Entity\Attribute as AttributeEntity;
use App\Event\Attribute\Created;
use App\Event\Attribute\Deleted;
use App\Event\Attribute\DeletedMulti;
use App\Event\Attribute\Updated;
use App\Repository\AttributeInterface;
use App\Validator\Attribute as AttributeValidator;
use Interop\Container\ContainerInterface;
use League\Event\Emitter;

/**
 * Handles Attribute commands.
 */
class Attribute implements HandlerInterface {
    /**
     * Attribute Repository instance.
     *
     * @var App\Repository\AttributeInterface
     */
    protected $repository;
    /**
     * Attribute Validator instance.
     *
     * @var App\Validator\Attribute
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
        $container[self::class] = function (ContainerInterface $container) {
            return new \App\Handler\Attribute(
                $container
                    ->get('repositoryFactory')
                    ->create('Attribute'),
                $container
                    ->get('validatorFactory')
                    ->create('Attribute'),
                $container
                    ->get('eventEmitter')
            );
        };
    }

    /**
     * Class constructor.
     *
     * @param App\Repository\AttributeInterface $repository
     * @param App\Validator\Attribute           $validator
     *
     * @return void
     */
    public function __construct(
        AttributeInterface $repository,
        AttributeValidator $validator,
        Emitter $emitter
    ) {
        $this->repository = $repository;
        $this->validator  = $validator;
        $this->emitter    = $emitter;
    }

    /**
     * Creates a new attribute data in the given user.
     *
     * @param App\Command\Attribute\CreateNew $command
     *
     * @return App\Entity\Attribute
     */
    public function handleCreateNew(CreateNew $command) : AttributeEntity {
        $this->validator->assertName($command->name);
        $this->validator->assertValue($command->value);

        $attribute = $this->repository->create(
            [
            'user_id'    => $command->user->id,
            'name'       => $command->name,
            'value'      => $command->value,
            'created_at' => time()
            ]
        );

        try {
            $attribute = $this->repository->save($attribute);
            $event     = new Created($attribute);
            $this->emitter->emit($event);
        } catch (\Exception $e) {
            throw new AppException('Error while creating an attribute');
        }

        return $attribute;
    }

    /**
     * Updates a attribute data from a given user.
     *
     * @param App\Command\Attribute\UpdateOne $command
     *
     * @return App\Entity\Attribute
     */
    public function handleUpdateOne(UpdateOne $command) : AttributeEntity {
        $this->validator->assertValue($command->value);

        $attribute        = $this->repository->findOneByUserIdAndName($command->user->id, $command->name);
        $attribute->value = $command->value;

        try {
            $attribute = $this->repository->save($attribute);
            $event     = new Updated($attribute);
            $this->emitter->emit($event);
        } catch (\Exception $e) {
            throw new AppException('Error while updating attribute');
        }

        return $attribute;
    }

    /**
     * Deletes a attribute data from a given user.
     *
     * @param App\Command\Attribute\DeleteOne $command
     *
     * @return int
     */
    public function handleDeleteOne(DeleteOne $command) : int {
        $this->validator->assertName($command->name);

        $attribute = $this->repository->findOneByUserIdAndName($command->user->id, $command->name);

        try {
            $affectedRows = $this->repository->deleteOneByUserIdAndName($command->user->id, $command->name);
            $event        = new Deleted($attribute);
            $this->emitter->emit($event);
        } catch (\Exception $e) {
            throw new AppException('Error while deleting attribute');
        }

        return $affectedRows;
    }

    /**
     * Deletes all attribute data from a given user.
     *
     * @param App\Command\Attribute\DeleteAll $command
     *
     * @return int
     */
    public function handleDeleteAll(DeleteAll $command) : int {
        $attributes = $this->repository->getAllByUserIdAndNames($command->user->id, $command->filters ?: []);

        try {
            $affectedRows = $this->repository->deleteByUserId($command->user->id, $command->filters ?: []);
            $event        = new DeletedMulti($attributes);
            $this->emitter->emit($event);
        } catch (\Exception $e) {
            throw new AppException('Error while deleting attributes');
        }

        return $affectedRows;
    }
}
