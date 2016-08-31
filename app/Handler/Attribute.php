<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types=1);

namespace App\Handler;

use App\Command\Attribute\CreateNew;
use App\Command\Attribute\DeleteAll;
use App\Command\Attribute\DeleteOne;
use App\Command\Attribute\UpdateOne;
use App\Entity\Attribute as AttributeEntity;
use App\Repository\AttributeInterface;
use App\Validator\Attribute as AttributeValidator;
use Interop\Container\ContainerInterface;

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
                    ->create('Attribute')
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
        AttributeValidator $validator
    ) {
        $this->repository = $repository;
        $this->validator  = $validator;
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

        $attribute = $this->repository->create([
            'user_id'    => $command->user->id,
            'name'       => $command->name,
            'value'      => $command->value,
            'created_at' => time()
        ]);

        $attribute = $this->repository->save($attribute);

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
        $attribute        = $this->repository->save($attribute);

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

        return $this->repository->deleteOneByUserIdAndName($command->user->id, $command->name);
    }

    /**
     * Deletes all attribute data from a given user.
     *
     * @param App\Command\Attribute\DeleteAll $command
     *
     * @return int
     */
    public function handleDeleteAll(DeleteAll $command) : int {
        return $this->repository->deleteByUserId($command->user->id);
    }

}
