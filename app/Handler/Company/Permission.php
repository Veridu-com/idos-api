<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Handler\Company;

use App\Command\Company\Permission\CreateNew;
use App\Command\Company\Permission\DeleteOne;
use App\Entity\Company\Permission as PermissionEntity;
use App\Exception\Create;
use App\Exception\NotFound;
use App\Exception\Validate;
use App\Factory\Event;
use App\Handler\HandlerInterface;
use App\Repository\Company\PermissionInterface;
use App\Validator\Company\Permission as PermissionValidator;
use Interop\Container\ContainerInterface;
use League\Event\Emitter;
use Respect\Validation\Exceptions\ValidationException;

/**e/Tags.php
    modified:   app/Route/Tasks.php
 * Handles Permission commands.
 */
class Permission implements HandlerInterface {
    /**
     * Permission Repository instance.
     *
     * @var \App\Repository\Company\PermissionInterface
     */
    private $repository;
    /**
     * Permission Validator instance.
     *
     * @var \App\Validator\Company\Permission
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
    public static function register(ContainerInterface $container) {
        $container[self::class] = function (ContainerInterface $container) : HandlerInterface {
            return new \App\Handler\Company\Permission(
                $container
                    ->get('repositoryFactory')
                    ->create('Company\Permission'),
                $container
                    ->get('validatorFactory')
                    ->create('Company\Permission'),
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
     * @param \App\Repository\Company\PermissionInterface $repository
     * @param \App\Validator\Company\Permission           $validator
     * @param \App\Factory\Event                          $eventFactory
     * @param \League\Event\Emitter                       $emitter
     *
     * @return void
     */
    public function __construct(
        PermissionInterface $repository,
        PermissionValidator $validator,
        Event $eventFactory,
        Emitter $emitter
    ) {
        $this->repository   = $repository;
        $this->validator    = $validator;
        $this->eventFactory = $eventFactory;
        $this->emitter      = $emitter;
    }

    /**
     * Creates a new Permission.
     *
     * @param \App\Command\Company\Permission\CreateNew $command
     *
     * @throws \App\Exception\Validate\Company\PermissionException
     * @throws \App\Exception\Create\Company\PermissionException
     *
     * @return \App\Entity\Company\Permission
     */
    public function handleCreateNew(CreateNew $command) : PermissionEntity {
        try {
            $this->validator->assertRouteName($command->routeName);
            $this->validator->assertId($command->companyId);
        } catch (ValidationException $e) {
            throw new Validate\Company\PermissionException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        $permission = $this->repository->create(
            [
                'route_name' => $command->routeName,
                'company_id' => $command->companyId,
                'created_at' => time()
            ]
        );

        try {
            $permission = $this->repository->save($permission);
            $event      = $this->eventFactory->create('Company\\Permission\\Created', $permission);
            $this->emitter->emit($event);
        } catch (\Exception $e) {
            throw new Create\Company\PermissionException('Error while trying to create a permission', 500, $e);
        }

        return $permission;
    }

    /**
     * Deletes a Permission.
     *
     * @param \App\Command\Company\Permission\DeleteOne $command
     *
     * @throws \App\Exception\Validate\PermissionException
     * @throws \App\Exception\NotFound\PermissionException
     *
     * @see \App\Repository\DBPermission::findOne
     * @see \App\Repository\DBPermission::deleteOne
     *
     * @return void
     */
    public function handleDeleteOne(DeleteOne $command) {
        try {
            $this->validator->assertId($command->companyId);
            $this->validator->assertRouteName($command->routeName);
        } catch (ValidationException $e) {
            throw new Validate\Company\PermissionException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        $permission = $this->repository->findOne($command->companyId, $command->routeName);

        $affectedRows = $this->repository->deleteOne($command->companyId, $command->routeName);

        if (! $affectedRows) {
            throw new NotFound\Company\PermissionException('No permissions found for deletion', 404);
        }

        $event = $this->eventFactory->create('Company\\Permission\\Deleted', $permission);
        $this->emitter->emit($event);
    }

    /**
     * Deletes all permissions ($command->companyId).
     *
     * @param \App\Command\Company\Permission\DeleteAll $command
     *
     * @see \App\Repository\DBPermission::getAllByComanyId
     * @see \App\Repository\DBPermission::deleteByCompanyId
     *
     * @throws \App\Exception\Validate\Company\PermissionException
     *
     * @return int
     */
    public function handleDeleteAll(DeleteAll $command) : int {
        try {
            $this->validator->assertId($command->companyId);
        } catch (ValidationException $e) {
            throw new Validate\Company\PermissionException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        $permissions = $this->repository->getByCompanyId($command->companyId);

        $affectedRows = $this->repository->deleteByCompanyId($command->companyId);

        $event = $this->eventFactory->create('Company\\Permission\\DeletedMulti', $permissions);
        $this->emitter->emit($event);

        return $affectedRows;
    }
}
