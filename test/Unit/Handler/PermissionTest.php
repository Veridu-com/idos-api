<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace Test\Unit\Handler;

use App\Command\Company\Permission\CreateNew;
use App\Command\Company\Permission\DeleteAll;
use App\Command\Company\Permission\DeleteOne;
use App\Entity\Company\Permission as PermissionEntity;
use App\Factory\Entity as EntityFactory;
use App\Factory\Repository;
use App\Factory\Validator;
use App\Handler\Company\Permission;
use App\Repository\DBPermission;
use App\Repository\Company\PermissionInterface;
use App\Validator\Company\Permission as PermissionValidator;
use Illuminate\Support\Collection;
use Jenssegers\Optimus\Optimus;
use League\Event\Emitter;
use Slim\Container;
use Test\Unit\AbstractUnit;

class PermissionTest extends AbstractUnit {
    /*
     * Jenssengers\Optimus\Optimus $optimus
     */
    private $optimus;

    public function setUp() {
        $this->optimus = $this->getMockBuilder(Optimus::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testConstructCorrectInterface() {
        $repositoryMock = $this
            ->getMockBuilder(PermissionInterface::class)
            ->getMock();

        $validatorMock = $this
            ->getMockBuilder(PermissionValidator::class)
            ->getMock();

        $emitterMock = $this
            ->getMockBuilder(Emitter::class)
            ->getMock();

        $this->assertInstanceOf(
            'App\\Handler\\HandlerInterface',
            new Permission(
                $repositoryMock,
                $validatorMock,
                $emitterMock
            )
        );
    }

    public function testRegister() {
        $container = new Container();

        $repositoryMock = $this
            ->getMockBuilder(PermissionInterface::class)
            ->getMock();

        $repositoryFactoryMock = $this
            ->getMockBuilder(Repository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $repositoryFactoryMock
            ->method('create')
            ->willReturn($repositoryMock);

        $container['repositoryFactory'] = function () use ($repositoryFactoryMock) {
            return $repositoryFactoryMock;
        };

        $validatorMock = $this
            ->getMockBuilder(PermissionValidator::class)
            ->getMock();

        $validatorFactoryMock = $this
            ->getMockBuilder(Validator::class)
            ->disableOriginalConstructor()
            ->getMock();

        $validatorFactoryMock
            ->method('create')
            ->willReturn($validatorMock);

        $container['validatorFactory'] = function () use ($validatorFactoryMock) {
            return $validatorFactoryMock;
        };

        $emitterMock = $this
            ->getMockBuilder(Emitter::class)
            ->getMock();

        $container['eventEmitter'] = function () use ($emitterMock) {
            return $emitterMock;
        };

        Permission::register($container);
        $this->assertInstanceOf(Permission::class, $container[Permission::class]);
    }

    public function testHandleCreateNewInvalidPermissionName() {
        $repositoryMock = $this
            ->getMockBuilder(PermissionInterface::class)
            ->getMock();

        $emitterMock = $this
            ->getMockBuilder(Emitter::class)
            ->getMock();

        $handler = new Permission(
            $repositoryMock,
            new PermissionValidator(),
            $emitterMock
        );

        $this->setExpectedException('InvalidArgumentException');

        $commandMock = $this
            ->getMockBuilder(CreateNew::class)
            ->getMock();
        $commandMock->routeName = '';
        $commandMock->companyId = 1;

        $handler->handleCreateNew($commandMock);
    }

    public function testHandleCreateNew() {
        $permissionEntity = new PermissionEntity(
            [
                'company_id' => 1,
                'route_name' => 'companies:listAll'
            ],
            $this->optimus
        );

        $dbConnectionMock = $this->getMockBuilder('Illuminate\Database\ConnectionInterface')
            ->getMock();

        $entityFactory = new EntityFactory($this->optimus);
        $entityFactory->create('Company\Permission');

        $permissionRepository = $this->getMockBuilder(DBPermission::class)
            ->setMethods(['save'])
            ->setConstructorArgs([$entityFactory, $this->optimus, $dbConnectionMock])
            ->getMock();
        $permissionRepository
            ->expects($this->once())
            ->method('save')
            ->willReturn($permissionEntity);

        $emitterMock = $this
            ->getMockBuilder(Emitter::class)
            ->getMock();

        $handler = new Permission(
            $permissionRepository,
            new PermissionValidator(),
            $emitterMock
        );

        $command            = new CreateNew();
        $command->routeName = 'companies:listAll';
        $command->companyId = 1;

        $result = $handler->handleCreateNew($command);

        $this->assertSame('companies:listAll', $result->route_name);
        $this->assertSame(1, $result->company_id);
    }

    public function testHandleDeleteAllCompanyIdNotFound() {
        $dbConnectionMock = $this->getMockBuilder('Illuminate\Database\ConnectionInterface')
            ->getMock();
        $entityFactory = new EntityFactory($this->optimus);
        $entityFactory->create('Company\Permission');

        $permissionRepository = $this->getMockBuilder(DBPermission::class)
            ->setConstructorArgs([$entityFactory, $this->optimus, $dbConnectionMock])
            ->getMock();

        $emitterMock = $this
            ->getMockBuilder(Emitter::class)
            ->getMock();

        $handler = new Permission(
            $permissionRepository,
            new PermissionValidator(),
            $emitterMock
        );

        $this->setExpectedException('InvalidArgumentException');

        $commandMock = $this
            ->getMockBuilder(DeleteAll::class)
            ->disableOriginalConstructor()
            ->getMock();

        $commandMock->companyId = '';

        $handler->handleDeleteAll($commandMock);
    }

    public function testHandleDeleteAll() {
        $dbConnectionMock = $this->getMockBuilder('Illuminate\Database\ConnectionInterface')
            ->getMock();

        $entityFactory = new EntityFactory($this->optimus);
        $entityFactory->create('Company\Permission');

        $permissionRepository = $this->getMockBuilder(DBPermission::class)
            ->setConstructorArgs([$entityFactory, $this->optimus, $dbConnectionMock])
            ->setMethods(['deleteByCompanyId', 'getAllByCompanyId'])
            ->getMock();

        $permissionRepository
            ->method('deleteByCompanyId')
            ->will($this->returnValue(0));

        $permissionRepository
            ->method('getAllByCompanyId')
            ->will(
                $this->returnValue(
                    new Collection(
                        [
                            [
                                'id'         => 1,
                                'route_name' => 'companies:listAll'
                            ]
                        ]
                    )
                )
            );

        $emitterMock = $this
            ->getMockBuilder(Emitter::class)
            ->getMock();

        $handler = new Permission(
            $permissionRepository,
            new PermissionValidator(),
            $emitterMock
        );

        $commandMock = $this
            ->getMockBuilder(DeleteAll::class)
            ->disableOriginalConstructor()
            ->getMock();

        $commandMock->companyId = 0;
        $this->assertSame(0, $handler->handleDeleteAll($commandMock));
    }

    public function testHandleDeleteOneInvalidRouteName() {
        $repositoryMock = $this
            ->getMockBuilder(PermissionInterface::class)
            ->getMock();

        $emitterMock = $this
            ->getMockBuilder(Emitter::class)
            ->getMock();

        $handler = new Permission(
            $repositoryMock,
            new PermissionValidator(),
            $emitterMock
        );

        $this->setExpectedException('InvalidArgumentException');

        $commandMock = $this
            ->getMockBuilder(DeleteOne::class)
            ->disableOriginalConstructor()
            ->getMock();

        // not a valid routeName (less than 5 chars)
        $commandMock->routeName = '';
        $commandMock->companyId = 1;

        $handler->handleDeleteOne($commandMock);
    }

    public function testHandleDeleteOne() {
        $dbConnectionMock = $this->getMockBuilder('Illuminate\Database\ConnectionInterface')
            ->getMock();

        $entityFactory = new EntityFactory($this->optimus);
        $entityFactory->create('Company\Permission');

        $permissionRepository = $this->getMockBuilder(DBPermission::class)
            ->setConstructorArgs([$entityFactory, $this->optimus, $dbConnectionMock])
            ->setMethods(['deleteOne', 'findOne'])
            ->getMock();

        $permissionRepository
            ->method('deleteOne')
            ->will($this->returnValue(1));

        $entityMock = $this->getMockBuilder(PermissionEntity::class)
            ->disableOriginalConstructor()
            ->getMock();

        $permissionRepository
            ->method('findOne')
            ->will(
                $this->returnValue(
                    $entityMock
                )
            );

        $emitterMock = $this
            ->getMockBuilder(Emitter::class)
            ->getMock();

        $handler = new Permission(
            $permissionRepository,
            new PermissionValidator(),
            $emitterMock
        );

        $commandMock = $this
            ->getMockBuilder(DeleteOne::class)
            ->disableOriginalConstructor()
            ->getMock();

        $commandMock->companyId = 0;
        $commandMock->routeName = 'Companies:listAll';
        $this->assertSame(1, $handler->handleDeleteOne($commandMock));
    }
}
