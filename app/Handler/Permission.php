<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Handler;

use App\Command\Permission\CreateNew;
use App\Command\Permission\DeleteOne;
use App\Entity\Permission as PermissionEntity;
use App\Event\Permission\Created;
use App\Event\Permission\Deleted;
use App\Event\Permission\DeletedMulti;
use App\Exception\Create;
use App\Exception\NotFound;
use App\Exception\Validate;
use App\Repository\PermissionInterface;
use App\Validator\Permission as PermissionValidator;
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
     * @var App\Repository\PermissionInterface
     */
    protected $repository;
    /**
     * Permission Validator instance.
     *
     * @var App\Validator\Permission
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
        $container[self::class] = function (ContainerInterface $container) : HandlerInterface {
            return new \App\Handler\Permission(
                $container
                    ->get('repositoryFactory')
                    ->create('Permission'),
                $container
                    ->get('validatorFactory')
                    ->create('Permission'),
                $container
                    ->get('eventEmitter')
            );
        };
    }

    /**
     * Class constructor.
     *
     * @param App\Repository\PermissionInterface
     * @param App\Validator\Permission
     * @param \League\Event\Emitter
     *
     * @return void
     */
    public function __construct(
        PermissionInterface $repository,
        PermissionValidator $validator,
        Emitter $emitter
    ) {
        $this->repository = $repository;
        $this->validator  = $validator;
        $this->emitter    = $emitter;
    }

    /**
     * Creates a new Permission.
     *
     * @param App\Command\Permission\CreateNew $command
     *
     * @throws App\Exception\Validate\PermissionException
     * @throws App\Exception\Create\PermissionException
     *
     * @return App\Entity\Permission
     */
    public function handleCreateNew(CreateNew $command) : PermissionEntity {
        try {
            $this->validator->assertRouteName($command->routeName);
            $this->validator->assertId($command->companyId);
        } catch (ValidationException $e) {
            throw new Validate\PermissionException(
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
            $event      = new Created($permission);
            $this->emitter->emit($event);
        } catch (\Exception $e) {
            throw new Create\PermissionException('Error while trying to create a permission', 500, $e);
        }

        return $permission;
    }

    /**
     * Deletes all permissions ($command->companyId).
     *
     * @param App\Command\Permission\DeleteAll $command
     *
     * @see App\Repository\DBPermission::getAllByComanyId
     * @see App\Repository\DBPermission::deleteByCompanyId
     * @throws App\Exception\Validate\PermissionException
     *
     * @return int
     */
    public function handleDeleteAll(DeleteAll $command) : int {
        try {
            $this->validator->assertId($command->companyId);
        } catch (ValidationException $e) {
            throw new Validate\PermissionException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        $permissions = $this->repository->getAllByCompanyId($command->companyId);

        $affectedRows = $this->repository->deleteByCompanyId($command->companyId);

        $event = new DeletedMulti($permissions);
        $this->emitter->emit($event);

        return $affectedRows;
    }

    /**
     * Deletes a Permission.
     *
     * @param App\Command\Permission\DeleteOne $command
     *
     * @throws App\Exception\Validate\PermissionException
     * @throws App\Exception\NotFound\PermissionException
     * @see App\Repository\DBPermission::findOne
     * @see App\Repository\DBPermission::deleteOne
     *
     * @return void
     */
    public function handleDeleteOne(DeleteOne $command) {
        try {
            $this->validator->assertId($command->companyId);
            $this->validator->assertRouteName($command->routeName);
        } catch (ValidationException $e) {
            throw new Validate\PermissionException(
                $e->getFullMessage(),
                400,
                $e
            );
        }

        $permission = $this->repository->findOne($command->companyId, $command->routeName);

        $affectedRows = $this->repository->deleteOne($command->companyId, $command->routeName);

        if (! $affectedRows) {
            throw new NotFound\PermissionException('No permissions found for deletion', 404);
        }

        $event = new Deleted($permission);
        $this->emitter->emit($event);
    }
}
