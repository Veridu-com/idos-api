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
use App\Exception\Create;
use App\Exception\Update;
use App\Exception\Validate;
use App\Repository\TaskInterface;
use App\Validator\Task as TaskValidator;
use Interop\Container\ContainerInterface;
use League\Event\Emitter;
use Respect\Validation\Exceptions\ValidationException;

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
     * @throws App\Exception\Validate\TaskException
     * @throws App\Exception\Create\TaskException
     * @see App\Repository\DBTask::create
     * @see App\Repository\DBTask::save
     *
     * @return App\Entity\Task
     */
    public function handleCreateNew(CreateNew $command) : TaskEntity {
        try {
            $this->validator->assertName($command->name);
            $this->validator->assertName($command->event);
            $this->validator->assertBooleanOrNull($command->running);
            $this->validator->assertBooleanOrNull($command->success);
            $this->validator->assertId($command->processId);
        } catch (ValidationException $e) {
            throw new Validate\TaskException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        $task = $this->repository->create(
            [
                'name'       => $command->name,
                'event'      => $command->event,
                'running'    => $command->running,
                'success'    => $command->success,
                'message'    => $command->message,
                'process_id' => $command->processId,
                'created_at' => time()
            ]
        );

        try {
            $task  = $this->repository->save($task);
            $event = new Created($task);
            $this->emitter->emit($event);
        } catch (\Exception $e) {
            throw new Create\TaskException('Error while trying to create a task', 500, $e);
        }

        return $task;
    }

    /**
     * Updates a Task.
     *
     * @param App\Command\Task\UpdateOne $command
     *
     * @see App\Repository\DBTask::find
     * @see App\Repository\DBTask::save
     * @throws App\Exception\Validate\TaskException
     * @throws App\Exception\Update\TaskException
     *
     * @return App\Entity\Task
     */
    public function handleUpdateOne(UpdateOne $command) : TaskEntity {
        try {
            $this->validator->assertBooleanOrNull($command->success);
            $this->validator->assertId($command->id);

            $task = $this->repository->find($command->id);

            if ($command->name) {
                $this->validator->assertName($command->name);
                $task->name = $command->name;
            }

            if ($command->event) {
                $this->validator->assertName($command->event);
                $task->event = $command->event;
            }

            if ($command->running) {
                $this->validator->assertBooleanOrNull($command->running);
                $task->running = $command->running;
            }

            if ($command->success) {
                $this->validator->assertBooleanOrNull($command->success);
                $task->success = $command->success;
            }
        } catch (ValidationException $e) {
            throw new Validate\TaskException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        if ($command->message) {
            $task->message = $command->message;
        }

        $task->updatedAt = time();

        try {
            $task  = $this->repository->save($task);
            $event = new Updated($task);
            $this->emitter->emit($event);
        } catch (\Exception $e) {
            throw new Update\TaskException('Error while trying to update a task', 500, $e);
        }

        return $task;
    }
}
