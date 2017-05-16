<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Handler\Profile;

use App\Command\Profile\Process\CreateNew;
use App\Command\Profile\Process\UpdateOne;
use App\Entity\Profile\Process as ProcessEntity;
use App\Exception\Create;
use App\Exception\Update;
use App\Exception\Validate;
use App\Factory\Event;
use App\Handler\HandlerInterface;
use App\Repository\Profile\ProcessInterface;
use App\Validator\Profile\Process as ProcessValidator;
use Interop\Container\ContainerInterface;
use League\Event\Emitter;
use Respect\Validation\Exceptions\ValidationException;

/**
 * Handles Process commands.
 */
class Process implements HandlerInterface {
    /**
     * Process Repository instance.
     *
     * @var \App\Repository\Profile\ProcessInterface
     */
    private $repository;
    /**
     * Process Validator instance.
     *
     * @var \App\Validator\Profile\Process
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
        $container[self::class] = function (ContainerInterface $container) {
            return new \App\Handler\Profile\Process(
                $container
                    ->get('repositoryFactory')
                    ->create('Profile\Process'),
                $container
                    ->get('validatorFactory')
                    ->create('Profile\Process'),
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
     * @param \App\Repository\ProcessInterface $repository
     * @param \App\Validator\Process           $validator
     * @param \App\Factory\Event               $eventFactory
     * @param \League\Event\Emitter            $emitter
     *
     * @return void
     */
    public function __construct(
        ProcessInterface $repository,
        ProcessValidator $validator,
        Event $eventFactory,
        Emitter $emitter
    ) {
        $this->repository   = $repository;
        $this->validator    = $validator;
        $this->eventFactory = $eventFactory;
        $this->emitter      = $emitter;
    }

    /**
     * Creates a process.
     *
     * @param \App\Command\Profile\Process\CreateNew $command
     *
     * @see \App\Repository\DBProcess::create
     * @see \App\Repository\DBProcess::save
     *
     * @throws \App\Exception\Validate\ProcessException
     * @throws \App\Exception\Create\ProcessException
     *
     * @return \App\Entity\Process
     */
    public function handleCreateNew(CreateNew $command) : ProcessEntity {
        try {
            $this->validator->assertName($command->name);
            $this->validator->assertName($command->event);
            $this->validator->assertId($command->userId);
            $this->validator->assertCredential($command->credential);
        } catch (ValidationException $e) {
            throw new Validate\Profile\ProcessException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

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
            $event = $this->eventFactory->create('Profile\\Process\\Created', $process, $command->credential);
            $this->emitter->emit($event);
        } catch (\Exception $e) {
            throw new Create\Profile\ProcessException('Error while trying to create a process', 500, $e);
        }

        return $process;
    }

    /**
     * Updates a Process.
     *
     * @param \App\Command\Profile\Process\UpdateOne $command
     *
     * @see \App\Repository\DBProcess::find
     * @see \App\Repository\DBProcess::save
     *
     * @throws \App\Exception\Validate\ProcessException
     * @throws \App\Exception\Update\ProcessException
     *
     * @return \App\Entity\Process
     */
    public function handleUpdateOne(UpdateOne $command) : ProcessEntity {
        try {
            $this->validator->assertName($command->name);
            $this->validator->assertName($command->event);
            $this->validator->assertId($command->id);
            $this->validator->assertCredential($command->credential);
        } catch (ValidationException $e) {
            throw new Validate\Profile\ProcessException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        $process = $this->repository->find($command->id);

        $process->name      = $command->name;
        $process->event     = $command->event;
        $process->updatedAt = time();

        try {
            $process = $this->repository->save($process);
            $event   = $this->eventFactory->create('Profile\\Process\\Updated', $process, $command->credential);
            $this->emitter->emit($event);
        } catch (\Exception $e) {
            throw new Update\Profile\ProcessException('Error while trying to update a feature', 500, $e);
        }

        return $process;
    }
}
