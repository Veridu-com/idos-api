<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
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
use App\Repository\Profile\TaskInterface;
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
     * @var App\Repository\Profile\TaskInterface
     */
    private $repository;
    /**
     * Task Validator instance.
     *
     * @var App\Validator\Profile\Task
     */
    private $validator;
    /**
     * Event factory instance.
     *
     * @var App\Factory\Event
     */
    private $eventFactory;
    /**
     * Event emitter instance.
     *
     * @var League\Event\Emitter
     */
    private $emitter;

    /**
     * {@inheritdoc}
     */
    public static function register(ContainerInterface $container) {
        $container[self::class] = function (ContainerInterface $container) {
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
     * @param App\Repository\TaskInterface $repository
     * @param App\Validator\Task           $validator
     * @param App\Factory\Event            $eventFactory
     * @param \League\Event\Emitter        $emitter
     *
     * @return void
     */
    public function __construct(
        TaskInterface $repository,
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
     * @param App\Command\Profile\Task\CreateNew $command
     *
     * @throws App\Exception\Validate\TaskException
     * @throws App\Exception\Create\TaskException
     *
     * @see App\Repository\DBTask::create
     * @see App\Repository\DBTask::save
     *
     * @return App\Entity\Task
     */
    public function handleCreateNew(CreateNew $command) : TaskEntity {
        try {
            $this->validator->assertName($command->name);
            $this->validator->assertService($command->service);
            $this->validator->assertName($command->event);
            $this->validator->assertBooleanOrNull($command->running);
            $this->validator->assertBooleanOrNull($command->success);
            $this->validator->assertId($command->processId);
        } catch (ValidationException $e) {
            throw new Validate\Profile\TaskException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        $task = $this->repository->create(
            [
                'name'       => $command->name,
                'event'      => $command->event,
                'creator'    => $command->service,
                'running'    => $command->running,
                'success'    => $command->success,
                'message'    => $command->message,
                'process_id' => $command->processId,
                'created_at' => time()
            ]
        );

        try {
            $task  = $this->repository->save($task);
            $event = $this->eventFactory->create('Profile\\Task\\Created', $task);
            $this->emitter->emit($event);
        } catch (\Exception $e) {
            throw new Create\Profile\TaskException('Error while trying to create a task', 500, $e);
        }

        return $task;
    }

    /**
     * Updates a Task.
     *
     * @param App\Command\Profile\Task\UpdateOne $command
     *
     * @see App\Repository\DBTask::find
     * @see App\Repository\DBTask::save
     *
     * @throws App\Exception\Validate\TaskException
     * @throws App\Exception\Update\TaskException
     *
     * @return App\Entity\Task
     */
    public function handleUpdateOne(UpdateOne $command) : TaskEntity {
        try {
            $this->validator->assertBooleanOrNull($command->success);
            $this->validator->assertCredential($command->credential);
            $this->validator->assertUser($command->user);
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
            throw new Validate\Profile\TaskException(
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

            $updated = $this->eventFactory->create('Profile\\Task\\Updated', $task);
            $this->emitter->emit($updated);
            
            if ($task->success && ! $task->running) {
                $completed = $this->eventFactory->create('Profile\\Task\\Completed', $task, $command->user, $command->credential, $task->event);
                $this->emitter->emit($completed);
            }

        } catch (\Exception $e) {
            throw new Update\Profile\TaskException('Error while trying to update a task', 500, $e);
        }

        return $task;
    }
}
