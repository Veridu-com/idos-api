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
use App\Repository\RepositoryInterface;
use App\Validator\Company\Permission as PermissionValidator;
use Interop\Container\ContainerInterface;
use League\Event\Emitter;
use Respect\Validation\Exceptions\ValidationException;

/*
 * Handles Permission commands.
 */
class Permission implements HandlerInterface {
    /**
     * Permission Repository instance.
     *
     * @var \App\Repository\RepositoryInterface
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
    public static function register(ContainerInterface $container) : void {
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
     * @param \App\Repository\RepositoryInterface $repository
     * @param \App\Validator\Company\Permission   $validator
     * @param \App\Factory\Event                  $eventFactory
     * @param \League\Event\Emitter               $emitter
     *
     * @return void
     */
    public function __construct(
        RepositoryInterface $repository,
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
            $this->validator->assertRouteName($command->routeName, 'routeName');
            $this->validator->assertId($command->companyId, 'companyId');
            $this->validator->assertIdentity($command->identity, 'identity');
        } catch (ValidationException $exception) {
            throw new Validate\Company\PermissionException(
                $exception->getFullMessage(),
                400,
                $exception
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
            $event      = $this->eventFactory->create('Company\Permission\Created', $permission, $command->identity);
            $this->emitter->emit($event);
        } catch (\Exception $exception) {
            throw new Create\Company\PermissionException('Error while trying to create a permission', 500, $exception);
        }

        return $permission;
    }

    /**
     * Deletes a Permission.
     *
     * @param \App\Command\Company\Permission\DeleteOne $command
     *
     * @throws \App\Exception\Validate\Company\PermissionException
     * @throws \App\Exception\NotFound\Company\PermissionException
     *
     * @see \App\Repository\DBPermission::findOne
     * @see \App\Repository\DBPermission::deleteOne
     *
     * @return void
     */
    public function handleDeleteOne(DeleteOne $command) {
        try {
            $this->validator->assertId($command->companyId, 'companyId');
            $this->validator->assertRouteName($command->routeName, 'routeName');
            $this->validator->assertIdentity($command->identity, 'identity');
        } catch (ValidationException $exception) {
            throw new Validate\Company\PermissionException(
                $exception->getFullMessage(),
                400,
                $exception
            );
        }

        $permission = $this->repository->findOne($command->companyId, $command->routeName);

        $affectedRows = $this->repository->deleteOne($command->companyId, $command->routeName);

        if (! $affectedRows) {
            throw new NotFound\Company\PermissionException('No permissions found for deletion', 404);
        }

        $event = $this->eventFactory->create('Company\Permission\Deleted', $permission, $command->identity);
        $this->emitter->emit($event);
    }
}
