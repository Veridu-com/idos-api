<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Handler;

use App\Command\Task\CreateNew;
use App\Command\Task\UpdateOne;
use App\Entity\Task as TaskEntity;
use App\Event\Task\Created;
use App\Event\Task\Updated;
use App\Repository\TaskInterface;
use App\Validator\Task as TaskValidator;
use Interop\Container\ContainerInterface;
use League\Event\Emitter;

/**
 * Handles Task commands.
 */
class Task implements HandlerInterface {
    /**
     * Task Repository instance.
     *
     * @var App\Repository\TaskInterface
     */
    protected $repository;
    /**
     * Task Validator instance.
     *
     * @var App\Validator\Task
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
            return new \App\Handler\Task(
                $container
                    ->get('repositoryFactory')
                    ->create('Task'),
                $container
                    ->get('validatorFactory')
                    ->create('Task'),
                $container
                    ->get('eventEmitter')
            );
        };
    }

    /**
     * Class constructor.
     *
     * @param App\Repository\TaskInterface $repository
     * @param App\Validator\Task           $validator
     *
     * @return void
     */
    public function __construct(
        TaskInterface $repository,
        TaskValidator $validator,
        Emitter $emitter
    ) {
        $this->repository = $repository;
        $this->validator  = $validator;
        $this->emitter    = $emitter;
    }

    /**
     * Creates a task.
     *
     * @param App\Command\Task\CreateNew $command
     *
     * @return App\Entity\Task
     */
    public function handleCreateNew(CreateNew $command) : TaskEntity {
        $this->validator->assertName($command->name);
        $this->validator->assertName($command->event);
        $this->validator->assertBoolean($command->running);
        $this->validator->assertBoolean($command->success);
        $this->validator->assertId($command->processId);

        $task = $this->repository->create(
            [
                'name'       => $command->name,
                'event'      => $command->event,
                'running'      => $command->running,
                'success'      => $command->success,
                'message'      => $command->message,
                'process_id'    => $command->processId,
                'created_at' => time()
            ]
        );

        try {
            $this->repository->save($task);
            $event = new Created($task);
            $this->emitter->emit($event);
        } catch (Exception $e) {
            throw new AppException('Error while creating a task');
        }

        return $task;
    }

    /**
     * Updates a Task.
     *
     * @param App\Command\Task\UpdateOne $command
     *
     * @return App\Entity\Task
     */
    public function handleUpdateOne(UpdateOne $command) : TaskEntity {
        $this->validator->assertName($command->name);
        $this->validator->assertName($command->event);
        $this->validator->assertBoolean($command->running);
        $this->validator->assertBoolean($command->success);
        $this->validator->assertId($command->id);

        $task = $this->repository->find($command->id);

        $task->name       = $command->name;
        $task->event      = $command->event;
        $task->running      = $command->running;
        $task->success      = $command->success;
        $task->message      = $command->message;
        $task->process_id    = $command->processId;
        $task->updatedAt = time();

        try {
            $task = $this->repository->save($task);
            $event = new Updated($task);
            $this->emitter->emit($event);
        } catch (Exception $e) {
            throw new AppException('Error while updating a task' . $command->id);
        }

        return $task;
    }
}
