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
use App\Entity\ServiceHandler as ServiceHandlerEntity;
use App\Event\ServiceHandler\Created;
use App\Factory\Entity as EntityFactory;
use App\Factory\Repository;
use App\Factory\Validator;
use App\Handler\ServiceHandler;
use App\Repository\ServiceHandlerInterface;
use App\Repository\DBServiceHandler;
use App\Validator\ServiceHandler as ServiceHandlerValidator;
use League\Event\Emitter;
use Slim\Container;
use Test\Unit\AbstractUnit;

class ServiceHandlerTest extends AbstractUnit {
    public function testConstructCorrectInterface() {
        $repositoryMock = $this
            ->getMockBuilder(ServiceHandlerInterface::class)
            ->getMock();
        $validatorMock = $this
            ->getMockBuilder(ServiceHandlerValidator::class)
            ->getMock();

        $this->assertInstanceOf(
            'App\\Handler\\HandlerInterface',
            new ServiceHandler(
                $repositoryMock,
                $validatorMock
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
        $serviceHandlerEntity = new ServiceHandlerEntity([]);

        $dbConnectionMock = $this->getMockBuilder('Illuminate\Database\ConnectionInterface')
            ->getMock();

        $entityFactory = new EntityFactory();
        $entityFactory->create('ServiceHandler');

        $serviceHandlerRepository = $this->getMockBuilder(DBServiceHandler::class)
            ->setMethods(['save'])
            ->setConstructorArgs([$entityFactory, $dbConnectionMock])
            ->getMock();
        $serviceHandlerRepository
            ->expects($this->once())
            ->method('save')
            ->willReturn($serviceHandlerEntity);

        $handler = new ServiceHandler(
            $serviceHandlerRepository,
            new ServiceHandlerValidator()
        );

        $command           = new CreateNew();
        $command->name     = 'New Service Handler';
        $command->source     = 'email';
        $command->companyId     = 1;
        $command->serviceSlug    = 'slug';
        $command->authPassword     = 'Auth Password';
        $command->authUsername     = 'Auth Username';
        $command->location = 'http://localhost:8080';

        $result = $handler->handleCreateNew($command);
        $this->assertSame('New Service Handler', $result->name);
        $this->assertSame('new-service-handler', $result->slug);
        $this->assertSame('email', $result->source);
        $this->assertSame(1, $result->companyId);
        $this->assertSame('slug', $result->serviceSlug);
        $this->assertSame('Auth Password', $result->authPassword);
        $this->assertSame('Auth Username', $result->authUsername);
        $this->assertSame('http://localhost:8080', $result->location);
        $this->assertTrue(is_int($result->created_at));
    }

    public function testHandleUpdateOne() {
        $serviceHandlerEntity = new ServiceHandlerEntity(
            [
                'id'         => 1,
                'name'       => 'New Service Handler',
                'slug'       => 'new-service-handler',
                'source' => 'email',
                'location' => 'http://localhost:8080' ,
                'service-slug' => 'slug',
                'created_at' => time(),
                'updated_at' => time()
            ]
        );

        $dbConnectionMock = $this->getMockBuilder('Illuminate\Database\ConnectionInterface')
            ->getMock();

        $entityFactory = new EntityFactory();
        $entityFactory->create('ServiceHandler');

        $serviceHandlerRepository = $this->getMockBuilder(DBServiceHandler::class)
            ->setMethods(['findOne', 'save'])
            ->setConstructorArgs([$entityFactory, $dbConnectionMock])
            ->getMock();
        $serviceHandlerRepository
            ->expects($this->once())
            ->method('findOne')
            ->will($this->returnValue($serviceHandlerEntity));
        $serviceHandlerRepository
            ->expects($this->once())
            ->method('save')
            ->willReturn($serviceHandlerEntity);

        $handler = new ServiceHandler(
            $serviceHandlerRepository,
            new ServiceHandlerValidator()
        );

        $command            = new UpdateOne();
        $command->name     = 'New Service Handler';
        $command->slug = 'new-service-handler';
        $command->source     = 'email';
        $command->companyId     = 1;
        $command->serviceSlug    = 'slug';
        $command->authPassword     = 'Auth Password';
        $command->authUsername     = 'Auth Username';
        $command->location = 'http://localhost:8080';

        $result = $handler->handleUpdateOne($command);
        $this->assertSame('New Service Handler', $result->name);
        $this->assertSame('new-service-handler', $result->slug);
        $this->assertSame('email', $result->source);
        $this->assertSame('Auth Password', $result->authPassword);
        $this->assertSame('Auth Username', $result->authUsername);
        $this->assertSame('http://localhost:8080', $result->location);
        $this->assertTrue(is_int($result->created_at));
        $this->assertTrue(is_int($result->updated_at));
    }

    public function testHandleDeleteOneInvalidServiceSlug() {
        $repositoryMock = $this
            ->getMockBuilder(ServiceHandlerInterface::class)
            ->getMock();

        $handler = new ServiceHandler(
            $repositoryMock,
            new ServiceHandlerValidator()
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

        $entityFactory = new EntityFactory();
        $entityFactory->create('ServiceHandler');

        $serviceHandlerRepository = $this->getMockBuilder(DBServiceHandler::class)
            ->setMethods(['deleteOne'])
            ->setConstructorArgs([$entityFactory, $dbConnectionMock])
            ->getMock();
        $serviceHandlerRepository
            ->method('deleteOne')
            ->will($this->returnValue(1));

        $handler = new ServiceHandler(
            $serviceHandlerRepository,
            new ServiceHandlerValidator()
        );

        $commandMock = $this
            ->getMockBuilder(DeleteOne::class)
            ->disableOriginalConstructor()
            ->getMock();

        $commandMock->companyId = 1;
        $commandMock->slug = 'slug';
        $commandMock->serviceSlug ='new-service-handler';

        $this->assertEquals(1, $handler->handleDeleteOne($commandMock));
    }


    public function testHandleDeleteAllInvalidServiceSlug() {
        $repositoryMock = $this
            ->getMockBuilder(ServiceHandlerInterface::class)
            ->getMock();

        $handler = new ServiceHandler(
            $repositoryMock,
            new ServiceHandlerValidator()
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

        $handler = new ServiceHandler(
            $repositoryMock,
            new ServiceHandlerValidator()
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

        $entityFactory = new EntityFactory();
        $entityFactory->create('ServiceHandler');

        $serviceHandlerRepository = $this->getMockBuilder(DBServiceHandler::class)
            ->setMethods(['deleteByCompanyId'])
            ->setConstructorArgs([$entityFactory, $dbConnectionMock])
            ->getMock();
        $serviceHandlerRepository
            ->method('deleteByCompanyId')
            ->will($this->returnValue(3));

        $handler = new ServiceHandler(
            $serviceHandlerRepository,
            new ServiceHandlerValidator()
        );

        $commandMock = $this
            ->getMockBuilder(DeleteAll::class)
            ->disableOriginalConstructor()
            ->getMock();

        $commandMock->companyId = 1;

        $this->assertEquals(3, $handler->handleDeleteAll($commandMock));
    }
}
