<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Handler\Profile;

use App\Command\Profile\Task\CreateNew;
use App\Command\Profile\Task\UpdateOne;
use App\Entity\Profile\Task as TaskEntity;
use App\Exception\Create;
use App\Exception\Update;
use App\Exception\Validate;
use App\Factory\Event;
use App\Handler\HandlerInterface;
use App\Repository\RepositoryInterface;
use App\Validator\Profile\Task as TaskValidator;
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
     * @var \App\Repository\RepositoryInterface
     */
    private $repository;
    /**
     * Task Validator instance.
     *
     * @var \App\Validator\Profile\Task
     */
    private $validator;
    /**
     * Event factory instance.
     *
     * @var \App\Factory\Event
     */
    private $eventFactory;
    /**
     * Event emitter instance.
     *
     * @var \League\Event\Emitter
     */
    private $emitter;

    /**
     * {@inheritdoc}
     */
    public static function register(ContainerInterface $container) : void {
        $container[self::class] = function (ContainerInterface $container) : HandlerInterface {
            return new \App\Handler\Profile\Task(
                $container
                    ->get('repositoryFactory')
                    ->create('Profile\Task'),
                $container
                    ->get('validatorFactory')
                    ->create('Profile\Task'),
                $container
                    ->get('eventFactory'),
                $container
                    ->get('eventEmitter')
            );
        };
    }

    /**
     * Class constructor.
     *
     * @param \App\Repository\RepositoryInterface $repository
     * @param \App\Validator\Profile\Task         $validator
     * @param \App\Factory\Event                  $eventFactory
     * @param \League\Event\Emitter               $emitter
     *
     * @return void
     */
    public function __construct(
        RepositoryInterface $repository,
        TaskValidator $validator,
        Event $eventFactory,
        Emitter $emitter
    ) {
        $this->emitter            = $emitter;
        $this->validator          = $validator;
        $this->repository         = $repository;
        $this->eventFactory       = $eventFactory;
    }

    /**
     * Creates a task.
     *
     * @param \App\Command\Profile\Task\CreateNew $command
     *
     * @throws \App\Exception\Validate\Profile\TaskException
     * @throws \App\Exception\Create\Profile\TaskException
     *
     * @see \App\Repository\Profile\DBTask::create
     * @see \App\Repository\Profile\DBTask::save
     *
     * @return \App\Entity\Profile\Task
     */
    public function handleCreateNew(CreateNew $command) : TaskEntity {
        try {
            $this->validator->assertName($command->name, 'name');
            $this->validator->assertHandler($command->handler, 'handler');
            $this->validator->assertName($command->event, 'event');
            $this->validator->assertNullableBoolean($command->running, 'running');
            $this->validator->assertNullableBoolean($command->success, 'success');
            $this->validator->assertNullableString($command->message, 'message');
            $this->validator->assertId($command->processId, 'processId');
            $this->validator->assertCredential($command->credential, 'credential');
        } catch (ValidationException $exception) {
            throw new Validate\Profile\TaskException(
                $exception->getFullMessage(),
                400,
                $exception
            );
        }

        $task = $this->repository->create(
            [
                'name'       => $command->name,
                'event'      => $command->event,
                'creator'    => $command->handler->id,
                'running'    => $command->running,
                'success'    => $command->success,
                'message'    => $command->message,
                'process_id' => $command->processId,
                'created_at' => time()
            ]
        );

        try {
            $task  = $this->repository->save($task);
            $event = $this->eventFactory->create('Profile\Task\Created', $task, $command->credential);
            $this->emitter->emit($event);
        } catch (\Exception $exception) {
            throw new Create\Profile\TaskException('Error while trying to create a task', 500, $exception);
        }

        return $task;
    }

    /**
     * Updates a Task.
     *
     * @param \App\Command\Profile\Task\UpdateOne $command
     *
     * @see \App\Repository\Profile\DBTask::find
     * @see \App\Repository\Profile\DBTask::save
     *
     * @throws \App\Exception\Validate\Profile\TaskException
     * @throws \App\Exception\Update\Profile\TaskException
     *
     * @return \App\Entity\Profile\Task
     */
    public function handleUpdateOne(UpdateOne $command) : TaskEntity {
        try {
            $this->validator->assertUser($command->user, 'user');
            $this->validator->assertId($command->id, 'id');

            $task = $this->repository->find($command->id);

            $updated = false;
            if ($command->running !== null) {
                $this->validator->assertBoolean($command->running, 'running');
                $task->running = $command->running;
                $updated       = true;
            }

            if ($command->success !== null) {
                $this->validator->assertBoolean($command->success, 'success');
                $task->success = $command->success;
                $updated       = true;
            }

            if ($command->message !== null) {
                $this->validator->assertString($command->message, 'message');
                $task->message = $command->message;
                $updated       = true;
            }

            if (! $updated) {
                return $task;
            }

            $this->validator->assertCredential($command->credential, 'credential');
        } catch (ValidationException $exception) {
            throw new Validate\Profile\TaskException(
                $exception->getFullMessage(),
                400,
                $exception
            );
        }

        $task->updatedAt = time();

        try {
            $task = $this->repository->save($task);

            $updated = $this->eventFactory->create('Profile\Task\Updated', $task, $command->credential);
            $this->emitter->emit($updated);

            if (! $task->running && $task->success) {
                $completed = $this->eventFactory->create('Profile\Task\Completed', $task, $command->user, $task->event, $command->credential);
                $this->emitter->emit($completed);
            }
        } catch (\Exception $exception) {
            throw new Update\Profile\TaskException('Error while trying to update a task', 500, $exception);
        }

        return $task;
    }
}
