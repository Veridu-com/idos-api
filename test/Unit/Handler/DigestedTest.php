<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace Test\Unit\Handler;

use App\Command\Digested\CreateNew;
use App\Command\Digested\DeleteAll;
use App\Command\Digested\DeleteOne;
use App\Command\Digested\UpdateOne;
use App\Entity\Digested as DigestedEntity;
use App\Factory\Entity as EntityFactory;
use App\Factory\Repository;
use App\Factory\Validator;
use App\Handler\Digested;
use App\Repository\DBDigested;
use App\Repository\DigestedInterface;
use App\Validator\Digested as DigestedValidator;
use Illuminate\Support\Collection;
use Jenssegers\Optimus\Optimus;
use League\Event\Emitter;
use Slim\Container;
use Test\Unit\AbstractUnit;

class DigestedTest extends AbstractUnit {
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

        return new DigestedEntity(
            [
                'id'         => $id,
                'source_id'  => $sourceId,
                'name'       => 'digested-' . $id,
                'value'      => 'value-' . $id,
                'created_at' => time()
            ],
            $optimus
        );
    }

    public function testConstructCorrectInterface() {
        $repositoryMock = $this
            ->getMockBuilder(DigestedInterface::class)
            ->getMock();
        $validatorMock = $this
            ->getMockBuilder(DigestedValidator::class)
            ->getMock();
        $emitterMock = $this
            ->getMockBuilder(Emitter::class)
            ->getMock();

        $this->assertInstanceOf(
            'App\\Handler\\HandlerInterface',
            new Digested(
                $repositoryMock,
                $validatorMock,
                $emitterMock
            )
        );
    }

    public function testRegister() {
        $container = new Container();

        $repositoryMock = $this
            ->getMockBuilder(DigestedInterface::class)
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
            ->getMockBuilder(DigestedValidator::class)
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

        Digested::register($container);
        $this->assertInstanceOf(Digested::class, $container[Digested::class]);
    }

    public function testHandleCreateNewInvalidDigestedName() {
        $repositoryMock = $this
            ->getMockBuilder(DigestedInterface::class)
            ->getMock();

        $emitterMock = $this
            ->getMockBuilder(Emitter::class)
            ->getMock();

        $handler = new Digested(
            $repositoryMock,
            new DigestedValidator(),
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
        $digestedEntity = $this->getEntity(1, 1);

        $dbConnectionMock = $this->getMockBuilder('Illuminate\Database\ConnectionInterface')
            ->getMock();

        $entityFactory = new EntityFactory($this->optimus);
        $entityFactory->create('Digested');

        $digestedRepository = $this->getMockBuilder(DBDigested::class)
            ->setMethods(['save'])
            ->setConstructorArgs([$entityFactory, $this->optimus, $dbConnectionMock])
            ->getMock();
        $digestedRepository
            ->expects($this->once())
            ->method('save')
            ->willReturn($digestedEntity);

        $handler = new Digested(
            $digestedRepository,
            new DigestedValidator()
        );

        $command           = new CreateNew();
        $command->sourceId = 1;
        $command->name     = 'digested-1';
        $command->value    = 'value-1';

        $result = $handler->handleCreateNew($command);
        $this->assertSame('digested-1', $result->name);
        $this->assertSame('value-1', $result->value);
    }

    public function testHandleUpdateOne() {
        $digestedEntity = $this->getEntity(1, 1);

        $dbConnectionMock = $this->getMockBuilder('Illuminate\Database\ConnectionInterface')
            ->getMock();

        $entityFactory = new EntityFactory($this->optimus);
        $entityFactory->create('Digested');

        $digestedRepository = $this->getMockBuilder(DBDigested::class)
            ->setMethods(['findOneByUserIdSourceIdAndName'])
            ->setConstructorArgs([$entityFactory, $this->optimus, $dbConnectionMock])
            ->getMock();
        $digestedRepository
            ->expects($this->once())
            ->method('findOneByUserIdSourceIdAndName')
            ->will(
                $this->returnValueMap(
                    [[
                    1,
                    $digestedEntity->sourceId,
                    $digestedEntity->name,
                    $digestedEntity
                    ]]
                )
            );

        $digestedRepository
            ->expects($this->once())
            ->method('save')
            ->willReturn($digestedEntity);

        $handler = new Digested(
            $digestedRepository,
            new DigestedValidator()
        );

        $command           = new UpdateOne();
        $command->name     = 'digested-1';
        $command->sourceId = 1;
        $command->value    = 'value-changed';

        $result = $handler->handleUpdateOne($command);
        $this->assertSame('digested-1', $result->name);
        $this->assertSame('value-changed', $result->value);
    }

    public function testHandleDeleteOne() {
        $dbConnectionMock = $this->getMockBuilder('Illuminate\Database\ConnectionInterface')
            ->getMock();

        $entityFactory = new EntityFactory($this->optimus);
        $entityFactory->create('Digested');

        $digestedRepository = $this->getMockBuilder(DBDigested::class)
            ->setMethods(['deleteOneBySourceIdAndName'])
            ->setConstructorArgs([$entityFactory, $this->optimus, $dbConnectionMock])
            ->getMock();

        $digestedRepository
            ->method('deleteOneBySourceIdAndName')
            ->will(
                $this->returnValueMap(
                    [[
                    1,
                    'digested-1',
                    1
                    ]]
                )
            );

        $handler = new Digested(
            $digestedRepository,
            new DigestedValidator()
        );

        $commandMock = $this
            ->getMockBuilder(DeleteOne::class)
            ->disableOriginalConstructor()
            ->getMock();

        $commandMock->name     = 'digested-1';
        $commandMock->sourceId = 1;

        $this->assertSame(1, $handler->handleDeleteOne($commandMock));
    }

    public function testHandleDeleteAll() {
        $dbConnectionMock = $this->getMockBuilder('Illuminate\Database\ConnectionInterface')
            ->getMock();

        $entityFactory = new EntityFactory($this->optimus);
        $entityFactory->create('Digested');

        $digestedRepository = $this->getMockBuilder(DBDigested::class)
            ->setMethods(['deleteBySourceId'])
            ->setConstructorArgs([$entityFactory, $this->optimus, $dbConnectionMock])
            ->getMock();

        $digestedRepository
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

        $handler = new Digested(
            $digestedRepository,
            new DigestedValidator()
        );

        $commandMock = $this
            ->getMockBuilder(DeleteAll::class)
            ->getMock();

        $commandMock->sourceId = 1;

        $this->assertSame(1, $handler->handleDeleteAll($commandMock));
    }
}
