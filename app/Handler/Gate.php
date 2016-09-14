<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Handler;

use App\Command\Gate\CreateNew;
use App\Command\Gate\DeleteAll;
use App\Command\Gate\DeleteOne;
use App\Command\Gate\UpdateOne;
use App\Entity\Gate as GateEntity;
use App\Event\Gate\Created;
use App\Event\Gate\Deleted;
use App\Event\Gate\DeletedMulti;
use App\Event\Gate\Updated;
use App\Exception\Create;
use App\Exception\NotFound;
use App\Exception\Update;
use App\Exception\Validate;
use App\Repository\GateInterface;
use App\Validator\Gate as GateValidator;
use Interop\Container\ContainerInterface;
use League\Event\Emitter;
use Respect\Validation\Exceptions\ValidationException;

/**
 * Handles Gate commands.
 */
class Gate implements HandlerInterface {
    /**
     * Gate Repository instance.
     *
     * @var App\Repository\GateInterface
     */
    protected $repository;
    /**
     * Gate Validator instance.
     *
     * @var App\Validator\Gate
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
        $container[self::class] = function (ContainerInterface $container) {
            return new \App\Handler\Gate(
                $container
                    ->get('repositoryFactory')
                    ->create('Gate'),
                $container
                    ->get('validatorFactory')
                    ->create('Gate'),
                $container
                    ->get('eventEmitter')
            );
        };
    }

    /**
     * Class constructor.
     *
     * @param App\Repository\GateInterface $repository
     * @param App\Validator\Gate           $validator
     *
     * @return void
     */
    public function __construct(
        GateInterface $repository,
        GateValidator $validator,
        Emitter $emitter
    ) {
        $this->repository = $repository;
        $this->validator  = $validator;
        $this->emitter    = $emitter;
    }

    /**
     * Creates a gate.
     *
     * @param App\Command\Gate\CreateNew $command
     *
     * @return App\Entity\Gate
     */
    public function handleCreateNew(CreateNew $command) : GateEntity {
        try {
            $this->validator->assertName($command->name);
            $this->validator->assertId($command->userId);
            $this->validator->assertBoolean($command->pass);
        } catch (ValidationException $e) {
            throw new Validate\GateException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        $gate = $this->repository->create(
            [
                'name'       => $command->name,
                'pass'       => $command->pass,
                'user_id'    => $command->userId,
                'created_at' => time()
            ]
        );

        try {
            $gate  = $this->repository->save($gate);
            $event = new Created($gate);
            $this->emitter->emit($event);
        } catch (\Exception $exception) {
            throw new Create\GateException('Error while trying to create a gate', 500, $e);
        }

        return $gate;
    }

    /**
     * Updates a Gate.
     *
     * @param App\Command\Gate\UpdateOne $command
     *
     * @return App\Entity\Gate
     */
    public function handleUpdateOne(UpdateOne $command) : GateEntity {
        try {
            $this->validator->assertSlug($command->gateSlug);
            $this->validator->assertBoolean($command->pass);
            $this->validator->assertId($command->userId);
        } catch (ValidationException $e) {
            throw new Validate\GateException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        $gate = $this->repository->findByUserIdAndSlug($command->userId, $command->gateSlug);

        $gate->pass      = $command->pass;
        $gate->updatedAt = time();

        try {
            $gate  = $this->repository->save($gate);
            $event = new Updated($gate);
            $this->emitter->emit($event);
        } catch (\Exception $exception) {
            throw new Update\GateException('Error while trying to update a gate', 500, $e);
        }

        return $gate;
    }

    /**
     * Deletes all gates ($command->userId).
     *
     * @param App\Command\Gate\DeleteAll $command
     *
     * @return int
     */
    public function handleDeleteAll(DeleteAll $command) : int {
        try {
            $this->validator->assertId($command->userId);
        } catch (ValidationException $e) {
            throw new Validate\GateException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        $deletedGates = $this->repository->findByUserId($command->userId);

        $rowsAffected = $this->repository->deleteByUserId($command->userId);

        $event = new DeletedMulti($deletedGates);
        $this->emitter->emit($event);

        return $rowsAffected;
    }

    /**
     * Deletes a Gate.
     *
     * @param App\Command\Gate\DeleteOne $command
     *
     * @return void
     */
    public function handleDeleteOne(DeleteOne $command) {
        try {
            $this->validator->assertSlug($command->gateSlug);
            $this->validator->assertId($command->userId);
        } catch (ValidationException $e) {
            throw new Validate\GateException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        $gate = $this->repository->findByUserIdAndSlug($command->userId, $command->gateSlug);

        $rowsAffected = $this->repository->delete($gate->id);

        if (! $rowsAffected) {
            throw new NotFound\GateException('No gates found for deletion', 404);
        }

        $event = new Deleted($gate);
        $this->emitter->emit($event);
    }
}
