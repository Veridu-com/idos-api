<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace Test\Unit\Handler;

use App\Command\Mapped\CreateNew;
use App\Command\Mapped\DeleteAll;
use App\Command\Mapped\DeleteOne;
use App\Command\Mapped\UpdateOne;
use App\Entity\Mapped as MappedEntity;
use App\Factory\Entity as EntityFactory;
use App\Factory\Repository;
use App\Factory\Validator;
use App\Handler\Mapped;
use App\Repository\DBMapped;
use App\Repository\MappedInterface;
use App\Validator\Mapped as MappedValidator;
use Illuminate\Support\Collection;
use Jenssegers\Optimus\Optimus;
use League\Event\Emitter;
use Slim\Container;
use Test\Unit\AbstractUnit;

class MappedTest extends AbstractUnit {
    /*
     * Jenssengers\Optimus\Optimus $optimus
     */
    private $optimus;

    public function setUp() {
        $this->optimus = $this->getMockBuilder(Optimus::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    private function getEntity($sourceId, $id) {
        $optimus = $this->getMockBuilder(Optimus::class)
            ->disableOriginalConstructor()
            ->getMock();

        return new MappedEntity(
            [
                'id'         => $id,
                'source_id'  => $sourceId,
                'name'       => 'mapped-' . $id,
                'value'      => 'value-' . $id,
                'created_at' => time()
            ],
            $optimus
        );
    }

    public function testConstructCorrectInterface() {
        $repositoryMock = $this
            ->getMockBuilder(MappedInterface::class)
            ->getMock();
        $validatorMock = $this
            ->getMockBuilder(MappedValidator::class)
            ->getMock();
        $emitterMock = $this
            ->getMockBuilder(Emitter::class)
            ->getMock();

        $this->assertInstanceOf(
            'App\\Handler\\HandlerInterface',
            new Mapped(
                $repositoryMock,
                $validatorMock,
                $emitterMock
            )
        );
    }

    public function testRegister() {
        $container = new Container();

        $repositoryMock = $this
            ->getMockBuilder(MappedInterface::class)
            ->getMock();

        $repositoryFactoryMock = $this
            ->getMockBuilder(Repository::class)
            ->disableOriginalConstructor()
            ->getMock();
        $repositoryFactoryMock
            ->method('create')
            ->willReturn($repositoryMock);

        $emitterMock = $this
            ->getMockBuilder(Emitter::class)
            ->getMock();

        $container['repositoryFactory'] = function () use ($repositoryFactoryMock) {
            return $repositoryFactoryMock;
        };

        $container['eventEmitter'] = function () use ($emitterMock) {
            return $emitterMock;
        };

        $validatorMock = $this
            ->getMockBuilder(MappedValidator::class)
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

        Mapped::register($container);
        $this->assertInstanceOf(Mapped::class, $container[Mapped::class]);
    }

    public function testHandleCreateNewInvalidMappedName() {
        $repositoryMock = $this
            ->getMockBuilder(MappedInterface::class)
            ->getMock();

        $emitterMock = $this
            ->getMockBuilder(Emitter::class)
            ->getMock();

        $handler = new Mapped(
            $repositoryMock,
            new MappedValidator(),
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
        $mappedEntity = $this->getEntity(1, 1);

        $dbConnectionMock = $this->getMockBuilder('Illuminate\Database\ConnectionInterface')
            ->getMock();

        $entityFactory = new EntityFactory($this->optimus);
        $entityFactory->create('Mapped');

        $mappedRepository = $this->getMockBuilder(DBMapped::class)
            ->setMethods(['save'])
            ->setConstructorArgs([$entityFactory, $this->optimus, $dbConnectionMock])
            ->getMock();
        $mappedRepository
            ->expects($this->once())
            ->method('save')
            ->willReturn($mappedEntity);

        $handler = new Mapped(
            $mappedRepository,
            new MappedValidator()
        );

        $command           = new CreateNew();
        $command->sourceId = 1;
        $command->name     = 'mapped-1';
        $command->value    = 'value-1';

        $result = $handler->handleCreateNew($command);
        $this->assertSame('mapped-1', $result->name);
        $this->assertSame('value-1', $result->value);
    }

    public function testHandleUpdateOne() {
        $mappedEntity = $this->getEntity(1, 1);

        $dbConnectionMock = $this->getMockBuilder('Illuminate\Database\ConnectionInterface')
            ->getMock();

        $entityFactory = new EntityFactory($this->optimus);
        $entityFactory->create('Mapped');

        $mappedRepository = $this->getMockBuilder(DBMapped::class)
            ->setMethods(['findOneByUserIdSourceIdAndName'])
            ->setConstructorArgs([$entityFactory, $this->optimus, $dbConnectionMock])
            ->getMock();
        $mappedRepository
            ->expects($this->once())
            ->method('findOneByUserIdSourceIdAndName')
            ->will(
                $this->returnValueMap(
                    [[
                    1,
                    $mappedEntity->sourceId,
                    $mappedEntity->name,
                    $mappedEntity
                    ]]
                )
            );

        $mappedRepository
            ->expects($this->once())
            ->method('save')
            ->willReturn($mappedEntity);

        $handler = new Mapped(
            $mappedRepository,
            new MappedValidator()
        );

        $command           = new UpdateOne();
        $command->name     = 'mapped-1';
        $command->sourceId = 1;
        $command->value    = 'value-changed';

        $result = $handler->handleUpdateOne($command);
        $this->assertSame('mapped-1', $result->name);
        $this->assertSame('value-changed', $result->value);
    }

    public function testHandleDeleteOne() {
        $dbConnectionMock = $this->getMockBuilder('Illuminate\Database\ConnectionInterface')
            ->getMock();

        $entityFactory = new EntityFactory($this->optimus);
        $entityFactory->create('Mapped');

        $mappedRepository = $this->getMockBuilder(DBMapped::class)
            ->setMethods(['deleteOneBySourceIdAndName'])
            ->setConstructorArgs([$entityFactory, $this->optimus, $dbConnectionMock])
            ->getMock();

        $mappedRepository
            ->method('deleteOneBySourceIdAndName')
            ->will(
                $this->returnValueMap(
                    [[
                    1,
                    'mapped-1',
                    1
                    ]]
                )
            );

        $handler = new Mapped(
            $mappedRepository,
            new MappedValidator()
        );

        $commandMock = $this
            ->getMockBuilder(DeleteOne::class)
            ->disableOriginalConstructor()
            ->getMock();

        $commandMock->name     = 'mapped-1';
        $commandMock->sourceId = 1;

        $this->assertEquals(1, $handler->handleDeleteOne($commandMock));
    }

    public function testHandleDeleteAll() {
        $dbConnectionMock = $this->getMockBuilder('Illuminate\Database\ConnectionInterface')
            ->getMock();

        $entityFactory = new EntityFactory($this->optimus);
        $entityFactory->create('Mapped');

        $mappedRepository = $this->getMockBuilder(DBMapped::class)
            ->setMethods(['deleteBySourceId'])
            ->setConstructorArgs([$entityFactory, $this->optimus, $dbConnectionMock])
            ->getMock();

        $mappedRepository
            ->method('deleteBySourceId')
            ->will(
                $this->returnValueMap(
                    [[
                    1,
                    1
                    ]]
                )
            );

        $collectionMock = $this
            ->getMockBuilder(Collection::class)
            ->getMock();

        $handler = new Mapped(
            $mappedRepository,
            new MappedValidator()
        );

        $commandMock = $this
            ->getMockBuilder(DeleteAll::class)
            ->getMock();

        $commandMock->sourceId = 1;

        $this->assertEquals(1, $handler->handleDeleteAll($commandMock));
    }
}
