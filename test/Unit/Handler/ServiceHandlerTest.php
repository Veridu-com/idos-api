<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace Test\Unit\Handler;

use App\Command\ServiceHandler\CreateNew;
use App\Command\ServiceHandler\DeleteAll;
use App\Command\ServiceHandler\DeleteOne;
use App\Command\ServiceHandler\UpdateOne;
use App\Entity\Service;
use App\Entity\ServiceHandler as ServiceHandlerEntity;
use App\Factory\Entity as EntityFactory;
use App\Factory\Repository;
use App\Factory\Validator;
use App\Handler\ServiceHandler;
use App\Repository\DBServiceHandler;
use App\Repository\ServiceHandlerInterface;
use App\Validator\ServiceHandler as ServiceHandlerValidator;
use Illuminate\Support\Collection;
use Jenssegers\Optimus\Optimus;
use League\Event\Emitter;
use Slim\Container;
use Test\Unit\AbstractUnit;

class ServiceHandlerTest extends AbstractUnit {
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
            ->getMockBuilder(ServiceHandlerInterface::class)
            ->getMock();

        $validatorMock = $this
            ->getMockBuilder(ServiceHandlerValidator::class)
            ->getMock();

        $emitterMock = $this
            ->getMockBuilder(Emitter::class)
            ->getMock();

        $this->assertInstanceOf(
            'App\\Handler\\HandlerInterface',
            new ServiceHandler(
                $repositoryMock,
                $validatorMock,
                $emitterMock
            )
        );
    }

    public function testRegister() {
        $container = new Container();

        $repositoryMock = $this
            ->getMockBuilder(ServiceHandlerInterface::class)
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
            ->getMockBuilder(ServiceHandlerValidator::class)
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

        ServiceHandler::register($container);
        $this->assertInstanceOf(ServiceHandler::class, $container[ServiceHandler::class]);
    }

    public function testHandleCreateNewInvalidServiceHandlerName() {
        $repositoryMock = $this
            ->getMockBuilder(ServiceHandlerInterface::class)
            ->getMock();

        $emitterMock = $this
            ->getMockBuilder(Emitter::class)
            ->getMock();

        $handler = new ServiceHandler(
            $repositoryMock,
            new ServiceHandlerValidator(),
            $emitterMock
        );
        $this->setExpectedException('InvalidArgumentException');

        $commandMock = $this
            ->getMockBuilder(CreateNew::class)
            ->getMock();
        $commandMock->name = '';

        $handler->handleCreateNew($commandMock);
    }

    public function testHandleCreateNew() {
        $savedEntity = new ServiceHandlerEntity(
            [
                'id'         => 1,
                'service_id' => 1,
                'url'        => 'http://localhost:8080',
                'listens'    => ['listen1', 'listen2'],
                'company_id' => 1,
                'created_at' => time()
            ],
            $this->optimus
        );

        $dbConnectionMock = $this->getMockBuilder('Illuminate\Database\ConnectionInterface')
            ->getMock();

        $entityFactory = new EntityFactory($this->optimus);
        $entityFactory->create('ServiceHandler');

        $serviceHandlerRepository = $this->getMockBuilder(DBServiceHandler::class)
            ->setMethods(['save'])
            ->setConstructorArgs([$entityFactory, $this->optimus, $dbConnectionMock])
            ->getMock();

        $serviceHandlerRepository
            ->expects($this->once())
            ->method('save')
            ->willReturn($savedEntity);

        $emitterMock = $this
            ->getMockBuilder(Emitter::class)
            ->getMock();

        $handler = new ServiceHandler(
            $serviceHandlerRepository,
            new ServiceHandlerValidator(),
            $emitterMock
        );

        $command            = new CreateNew();
        $command->companyId = 1;
        $command->serviceId = 1;
        $command->url       = 'http://localhost:8080';
        $command->listens   = ['listen1', 'listen2'];

        $result = $handler->handleCreateNew($command);

        $this->assertSame(1, $result->companyId);
        $this->assertSame(1, $result->serviceId);
        $this->assertSame(['listen1', 'listen2'], $result->listens);
        $this->assertSame('http://localhost:8080', $result->url);
        $this->assertTrue(is_int($result->created_at));
    }

    public function testHandleUpdateOne() {
        $serviceHandlerEntity = new ServiceHandlerEntity(
            [
                'companyId'          => 1,
                'serviceHandlerId'   => 1,
                'listens'            => ['listen1', 'listen2'],
                'service.id'         => 1,
                'service'            => new Service(['id' => 1], $this->optimus),
                'service.name'       => 'my cool service',
                'service.created_at' => time(),
                'service.updated_at' => time(),
                'created_at'         => time(),
                'updated_at'         => time()
            ],
            $this->optimus
        );

        $dbConnectionMock = $this->getMockBuilder('Illuminate\Database\ConnectionInterface')
            ->getMock();

        $entityFactory = new EntityFactory($this->optimus);
        $entityFactory->create('ServiceHandler');

        $serviceHandlerRepository = $this->getMockBuilder(DBServiceHandler::class)
            ->setMethods(['findOne', 'save'])
            ->setConstructorArgs([$entityFactory, $this->optimus, $dbConnectionMock])
            ->getMock();

        $serviceHandlerRepository
            ->expects($this->once())
            ->method('findOne')
            ->will($this->returnValue($serviceHandlerEntity));

        $serviceHandlerEntity->relations = [
            'service' => new Service(
                [
                    'id'      => 1,
                    'listens' => ['listen1', 'listen2', 'listen3', 'listen4']
                ],
                $this->optimus
            )
        ];

        $serviceHandlerRepository
            ->expects($this->once())
            ->method('save')
            ->willReturn($serviceHandlerEntity);

        $emitterMock = $this
            ->getMockBuilder(Emitter::class)
            ->getMock();

        $handler = new ServiceHandler(
            $serviceHandlerRepository,
            new ServiceHandlerValidator(),
            $emitterMock
        );

        $command                   = new UpdateOne();
        $command->companyId        = 1;
        $command->serviceHandlerId = 1;
        $command->listens          = ['listen3', 'listen4'];

        $result = $handler->handleUpdateOne($command);

        $this->assertSame(['listen3', 'listen4'], $result->listens);
        $this->assertTrue(is_int($result->created_at));
        $this->assertTrue(is_int($result->updated_at));
    }

    public function testHandleDeleteOneInvalidServiceSlug() {
        $repositoryMock = $this
            ->getMockBuilder(ServiceHandlerInterface::class)
            ->getMock();

        $emitterMock = $this
            ->getMockBuilder(Emitter::class)
            ->getMock();

        $handler = new ServiceHandler(
            $repositoryMock,
            new ServiceHandlerValidator(),
            $emitterMock
        );

        $this->setExpectedException('InvalidArgumentException');

        $commandMock = $this
            ->getMockBuilder(DeleteOne::class)
            ->disableOriginalConstructor()
            ->getMock();
        $commandMock->serviceSlug = '';

        $handler->handleDeleteOne($commandMock);
    }

    public function testHandleDeleteOne() {
        $dbConnectionMock = $this->getMockBuilder('Illuminate\Database\ConnectionInterface')
            ->getMock();

        $entityFactory = new EntityFactory($this->optimus);
        $entityFactory->create('ServiceHandler');

        $serviceHandlerRepository = $this->getMockBuilder(DBServiceHandler::class)
            ->setMethods(['deleteOne', 'find'])
            ->setConstructorArgs([$entityFactory, $this->optimus, $dbConnectionMock])
            ->getMock();

        $serviceHandlerRepository
            ->method('deleteOne')
            ->will($this->returnValue(1));

        $entityMock = $this->getMockBuilder(ServiceHandlerEntity::class)
            ->disableOriginalConstructor()
            ->getMock();

        $serviceHandlerRepository
            ->method('find')
            ->will($this->returnValue($entityMock));

        $emitterMock = $this
            ->getMockBuilder(Emitter::class)
            ->getMock();

        $handler = new ServiceHandler(
            $serviceHandlerRepository,
            new ServiceHandlerValidator(),
            $emitterMock
        );

        $commandMock = $this
            ->getMockBuilder(DeleteOne::class)
            ->disableOriginalConstructor()
            ->getMock();

        $commandMock->companyId        = 1;
        $commandMock->serviceHandlerId = 1;

        $this->assertEquals(1, $handler->handleDeleteOne($commandMock));
    }

    public function testHandleDeleteAllInvalidServiceSlug() {
        $repositoryMock = $this
            ->getMockBuilder(ServiceHandlerInterface::class)
            ->getMock();

        $emitterMock = $this
            ->getMockBuilder(Emitter::class)
            ->getMock();

        $handler = new ServiceHandler(
            $repositoryMock,
            new ServiceHandlerValidator(),
            $emitterMock
        );

        $this->setExpectedException('InvalidArgumentException');

        $commandMock = $this
            ->getMockBuilder(DeleteAll::class)
            ->disableOriginalConstructor()
            ->getMock();
        $commandMock->serviceSlug = '';

        $handler->handleDeleteAll($commandMock);
    }

    public function testHandleDeleteAllCompanyIdNotFound() {
        $repositoryMock = $this
            ->getMockBuilder(ServiceHandlerInterface::class)
            ->getMock();

        $emitterMock = $this
            ->getMockBuilder(Emitter::class)
            ->getMock();

        $handler = new ServiceHandler(
            $repositoryMock,
            new ServiceHandlerValidator(),
            $emitterMock
        );

        $commandMock = $this
            ->getMockBuilder(DeleteAll::class)
            ->disableOriginalConstructor()
            ->getMock();

        $commandMock->companyId = null;

        $this->setExpectedException('InvalidArgumentException');
        $handler->handleDeleteAll($commandMock);
    }

    public function testHandleDeleteAll() {
        $dbConnectionMock = $this->getMockBuilder('Illuminate\Database\ConnectionInterface')
            ->getMock();

        $entityFactory = new EntityFactory($this->optimus);
        $entityFactory->create('ServiceHandler');

        $serviceHandlerRepository = $this->getMockBuilder(DBServiceHandler::class)
            ->setMethods(['deleteByCompanyId', 'findByCompanyId'])
            ->setConstructorArgs([$entityFactory, $this->optimus, $dbConnectionMock])
            ->getMock();

        $serviceHandlerRepository
            ->method('deleteByCompanyId')
            ->will($this->returnValue(3));

        $serviceHandlerRepository
            ->method('findByCompanyId')
            ->will(
                $this->returnValue(
                    new Collection(
                        [
                            [
                                'id' => 1
                            ],
                            [
                                'id' => 2
                            ],
                            [
                                'id' => 3
                            ]
                        ]
                    )
                )
            );

        $emitterMock = $this
            ->getMockBuilder(Emitter::class)
            ->getMock();

        $handler = new ServiceHandler(
            $serviceHandlerRepository,
            new ServiceHandlerValidator(),
            $emitterMock
        );

        $commandMock = $this
            ->getMockBuilder(DeleteAll::class)
            ->disableOriginalConstructor()
            ->getMock();

        $commandMock->companyId = 1;

        $this->assertEquals(3, $handler->handleDeleteAll($commandMock));
    }
}
