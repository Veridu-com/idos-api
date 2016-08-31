<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Handler;

use App\Command\Warning\CreateNew;
use App\Command\Warning\DeleteAll;
use App\Command\Warning\DeleteOne;
use App\Entity\Warning as WarningEntity;
use App\Event\Warning\Created;
use App\Event\Warning\Deleted;
use App\Event\Warning\DeletedMulti;
use App\Exception\AppException as AppException;
use App\Repository\WarningInterface;
use App\Validator\Warning as WarningValidator;
use Interop\Container\ContainerInterface;
use League\Event\Emitter;

/**
 * Handles Warning commands.
 */
class Warning implements HandlerInterface {
    /**
     * Warning Repository instance.
     *
     * @var App\Repository\WarningInterface
     */
    protected $repository;
    /**
     * Warning Validator instance.
     *
     * @var App\Validator\Warning
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
            return new \App\Handler\Warning(
                $container
                    ->get('repositoryFactory')
                    ->create('Warning'),
                $container
                    ->get('validatorFactory')
                    ->create('Warning'),
                $container
                    ->get('eventEmitter')
            );
        };
    }

    /**
     * Class constructor.
     *
     * @param App\Repository\WarningInterface $repository
     * @param App\Validator\Warning           $validator
     *
     * @return void
     */
    public function __construct(
        WarningInterface $repository,
        WarningValidator $validator,
        Emitter $emitter
    ) {
        $this->repository = $repository;
        $this->validator  = $validator;
        $this->emitter    = $emitter;
    }

    /**
     * Creates a warning.
     *
     * @param App\Command\Warning\CreateNew $command
     *
     * @return App\Entity\Warning
     */
    public function handleCreateNew(CreateNew $command) : WarningEntity {
        $this->validator->assertName($command->name);
        $this->validator->assertId($command->userId);

        $warning = $this->repository->create(
            [
                'name'       => $command->name,
                'user_id'    => $command->userId,
                'created_at' => time()
            ]
        );

        try {
            $warning = $this->repository->save($warning);
            $event   = new Created($warning);
            $this->emitter->emit($event);
        } catch (\Exception $exception) {
            throw new AppException('Error while creating a warning');
        }

        return $warning;
    }

    /**
     * Deletes all settings ($command->userId).
     *
     * @param App\Command\Warning\DeleteAll $command
     *
     * @return int
     */
    public function handleDeleteAll(DeleteAll $command) : int {
        $this->validator->assertId($command->userId);
        $deletedWarnings = $this->repository->findByUserId($command->userId);

        try {
            $rowsAffected = $this->repository->deleteByUserId($command->userId);
            $event        = new DeletedMulti($deletedWarnings);
            $this->emitter->emit($event);
        } catch (Exception $exception) {
            throw new AppException('Error while deleting all warnings under user ' . $command->userId);
        }

        return $rowsAffected;
    }

    /**
     * Deletes a Warning.
     *
     * @param App\Command\Warning\DeleteOne $command
     *
     * @return int
     */
    public function handleDeleteOne(DeleteOne $command) : int {
        $this->validator->assertSlug($command->warningSlug);
        $this->validator->assertId($command->userId);
        $warning = $this->repository->findByUserIdAndSlug($command->userId, $command->warningSlug);

        $rowsAffected = $this->repository->delete($warning->id);
        if ($rowsAffected) {
            $event = new Deleted($warning);
            $this->emitter->emit($event);
        }
        else
            throw new AppException('Error while deleting a warning id ' . $warning->id);

        return $rowsAffected;
    }
}
