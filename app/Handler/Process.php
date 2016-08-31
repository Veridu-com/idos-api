<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Handler;

use App\Command\Process\CreateNew;
use App\Command\Process\UpdateOne;
use App\Entity\Process as ProcessEntity;
use App\Event\Process\Created;
use App\Event\Process\Updated;
use App\Repository\ProcessInterface;
use App\Validator\Process as ProcessValidator;
use Interop\Container\ContainerInterface;
use League\Event\Emitter;

/**
 * Handles Process commands.
 */
class Process implements HandlerInterface {
    /**
     * Process Repository instance.
     *
     * @var App\Repository\ProcessInterface
     */
    protected $repository;
    /**
     * Process Validator instance.
     *
     * @var App\Validator\Process
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
            return new \App\Handler\Process(
                $container
                    ->get('repositoryFactory')
                    ->create('Process'),
                $container
                    ->get('validatorFactory')
                    ->create('Process'),
                $container
                    ->get('eventEmitter')
            );
        };
    }

    /**
     * Class constructor.
     *
     * @param App\Repository\ProcessInterface $repository
     * @param App\Validator\Process           $validator
     *
     * @return void
     */
    public function __construct(
        ProcessInterface $repository,
        ProcessValidator $validator,
        Emitter $emitter
    ) {
        $this->repository = $repository;
        $this->validator  = $validator;
        $this->emitter    = $emitter;
    }

    /**
     * Creates a process.
     *
     * @param App\Command\Process\CreateNew $command
     *
     * @return App\Entity\Process
     */
    public function handleCreateNew(CreateNew $command) : ProcessEntity {
        $this->validator->assertName($command->name);
        $this->validator->assertEvent($command->event);
        $this->validator->assertId($command->userId);

        $process = $this->repository->create(
            [
                'name'       => $command->name,
                'event'      => $command->event,
                'user_id'    => $command->userId,
                'created_at' => time()
            ]
        );

        try {
            $this->repository->save($process);
            $event = new Created($process);
            $this->emitter->emit($event);
        } catch (Exception $e) {
            throw new AppException('Error while creating a process');
        }

        return $process;
    }

    /**
     * Updates a Process.
     *
     * @param App\Command\Process\UpdateOne $command
     *
     * @return App\Entity\Process
     */
    public function handleUpdateOne(UpdateOne $command) : ProcessEntity {
        $this->validator->assertName($command->name);
        $this->validator->assertEvent($command->event);
        $this->validator->assertId($command->id);

        $process = $this->repository->find($command->id);

        $process->name     = $command->name;
        $process->event     = $command->event;
        $process->updatedAt = time();

        try {
            $process = $this->repository->save($process);
            $event = new Updated($process);
            $this->emitter->emit($event);
        } catch (Exception $e) {
            throw new AppException('Error while updating a process' . $command->id);
        }

        return $process;
    }
}
