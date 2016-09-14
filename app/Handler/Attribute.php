<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Handler;

use App\Command\Attribute\CreateNew;
use App\Command\Attribute\DeleteAll;
use App\Command\Attribute\DeleteOne;
use App\Command\Attribute\UpdateOne;
use App\Entity\Attribute as AttributeEntity;
use App\Event\Attribute\Created;
use App\Event\Attribute\Deleted;
use App\Event\Attribute\DeletedMulti;
use App\Event\Attribute\Updated;
use App\Exception\Create;
use App\Exception\NotFound;
use App\Exception\Update;
use App\Exception\Validate;
use App\Repository\AttributeInterface;
use App\Validator\Attribute as AttributeValidator;
use Interop\Container\ContainerInterface;
use League\Event\Emitter;
use Respect\Validation\Exceptions\ValidationException;

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
        try {
            $this->validator->assertName($command->name);
            $this->validator->assertValue($command->value);
        } catch (ValidationException $e) {
            throw new Validate\AttributeException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

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
            throw new Create\AttributeException('Error while trying to create an attribute', 500, $e);
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
        try {
            $this->validator->assertValue($command->value);
        } catch (ValidationException $e) {
            throw new Validate\AttributeException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        $attribute        = $this->repository->findOneByUserIdAndName($command->user->id, $command->name);
        $attribute->value = $command->value;

        try {
            $attribute = $this->repository->save($attribute);
            $event     = new Updated($attribute);
            $this->emitter->emit($event);
        } catch (\Exception $e) {
            throw new Update\AttributeException('Error while trying to update an attribute', 500, $e);
        }

        return $attribute;
    }

    /**
     * Deletes a attribute data from a given user.
     *
     * @param App\Command\Attribute\DeleteOne $command
     *
     * @return void
     */
    public function handleDeleteOne(DeleteOne $command) {
        try {
            $this->validator->assertName($command->name);
        } catch (ValidationException $e) {
            throw new Validate\AttributeException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        $attribute = $this->repository->findOneByUserIdAndName($command->user->id, $command->name);

        $affectedRows = $this->repository->deleteOneByUserIdAndName($command->user->id, $command->name);

        if (! $affectedRows) {
            throw new NotFound\AttributeException('No attributes found for deletion', 404);
        }

        $event = new Deleted($attribute);
        $this->emitter->emit($event);
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

        $affectedRows = $this->repository->deleteByUserId($command->user->id);
        $event        = new DeletedMulti($attributes);
        $this->emitter->emit($event);

        return $affectedRows;
    }
}
