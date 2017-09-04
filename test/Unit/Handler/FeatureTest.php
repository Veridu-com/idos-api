<?php
/*
 * Copyright (c) 2012-2017 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace Test\Unit\Handler;

use App\Command\Profile\Feature\CreateNew;
use App\Command\Profile\Feature\DeleteAll;
use App\Command\Profile\Feature\DeleteOne;
use App\Command\Profile\Feature\UpdateOne;
use App\Entity\Profile\Feature as FeatureEntity;
use App\Factory\Entity as EntityFactory;
use App\Factory\Repository;
use App\Factory\Validator;
use App\Handler\HandlerInterface;
use App\Handler\Profile\Feature;
use App\Repository\DBFeature;
use App\Repository\Profile\FeatureInterface;
use App\Validator\Profile\Feature as FeatureValidator;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Support\Collection;
use Jenssegers\Optimus\Optimus;
use League\Event\Emitter;
use Slim\Container;
use Test\Unit\AbstractUnit;

class FeatureTest extends AbstractUnit {
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
            ->getMockBuilder(FeatureInterface::class)
            ->getMock();

        $validatorMock = $this
            ->getMockBuilder(FeatureValidator::class)
            ->getMock();

        $emitterMock = $this
            ->getMockBuilder(Emitter::class)
            ->getMock();

        $this->assertInstanceOf(
            HandlerInterface::class,
            new Feature(
                $repositoryMock,
                $validatorMock,
                $emitterMock
            )
        );
    }

    public function testRegister() {
        $container = new Container();

        $repositoryMock = $this
            ->getMockBuilder(FeatureInterface::class)
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
            ->getMockBuilder(FeatureValidator::class)
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

        $emitterFactoryMock = $this
            ->getMockBuilder(Emitter::class)
            ->disableOriginalConstructor()
            ->getMock();

        $emitterFactoryMock
            ->method('create')
            ->willReturn($emitterMock);

        $container['emitterFactory'] = function () use ($emitterFactoryMock) {
            return $emitterFactoryMock;
        };

        Feature::register($container);
        $this->assertInstanceOf(Feature::class, $container[Feature::class]);
    }

    public function testHandleCreateNewInvalidFeatureProperties() {
        $repositoryMock = $this
            ->getMockBuilder(FeatureInterface::class)
            ->getMock();

        $emitterMock = $this
            ->getMockBuilder(Emitter::class)
            ->getMock();

        $handler = new Feature(
            $repositoryMock,
            new FeatureValidator(),
            $emitterMock
        );

        $this->expectedException('InvalidArgumentException');

        $commandMock = $this
            ->getMockBuilder(CreateNew::class)
            ->getMock();

        $commandMock->setParameters(
            [
                'name'    => 'name',
                'slug'    => 'slug',
                'value'   => 'value',
                'user_id' => 1
            ]
        );

        $handler->handleCreateNew($commandMock);
    }

    public function testHandleCreateNew() {
        $name   = 'Test';
        $slug   = 'test';
        $value  = 'value';
        $userId = 1;

        $featureEntity = new FeatureEntity(
            [
            'name'    => $name,
            'slug'    => $slug,
            'value'   => $value,
            'user_id' => $userId
            ], $this->optimus
        );

        $dbConnectionMock = $this->getMockBuilder(ConnectionInterface::class)
            ->getMock();

        $entityFactory = new EntityFactory($this->optimus);
        $entityFactory->create('Profile\Feature');

        $featureRepository = $this->getMockBuilder(DBFeature::class)
            ->setMethods(['save'])
            ->setConstructorArgs([$entityFactory, $this->optimus, $dbConnectionMock])
            ->getMock();

        $featureRepository
            ->expects($this->once())
            ->method('save')
            ->willReturn($featureEntity);

        $emitterMock = $this
            ->getMockBuilder(Emitter::class)
            ->getMock();

        $handler = new Feature(
            $featureRepository,
            new FeatureValidator(),
            $emitterMock
        );

        $command         = new CreateNew();
        $command->name   = $name;
        $command->value  = $value;
        $command->userId = $userId;

        $result = $handler->handleCreateNew($command);

        $this->assertSame($name, $result->name);
        $this->assertSame($slug, $result->slug);
        $this->assertSame($value, $result->value);
        $this->assertSame($userId, $result->userId);
    }

    public function testHandleDeleteAllInvalidUserId() {
        $repositoryMock = $this
            ->getMockBuilder(FeatureInterface::class)
            ->getMock();

        $emitterMock = $this
            ->getMockBuilder(Emitter::class)
            ->getMock();

        $handler = new Feature(
            $repositoryMock,
            new FeatureValidator(),
            $emitterMock
        );

        $this->expectedException('InvalidArgumentException');

        $commandMock = $this
            ->getMockBuilder(DeleteAll::class)
            ->disableOriginalConstructor()
            ->getMock();

        $commandMock->userId = '';

        $handler->handleDeleteAll($commandMock);
    }

    public function testHandleDeleteAll() {
        $dbConnectionMock = $this->getMockBuilder(ConnectionInterface::class)
            ->getMock();

        $entityFactory = new EntityFactory($this->optimus);
        $entityFactory->create('Profile\Feature');

        $amount         = 1;
        $repositoryMock = $this->getMockBuilder(DBFeature::class)
            ->setMethods(['findByUserId', 'deleteByUserId'])
            ->setConstructorArgs([$entityFactory, $this->optimus, $dbConnectionMock])
            ->getMock();

        $repositoryMock
            ->expects($this->once())
            ->method('deleteByUserId')
            ->willReturn($amount);

        $collectionMock = $this
            ->getMockBuilder(Collection::class)
            ->getMock();

        $repositoryMock
            ->expects($this->once())
            ->method('findByUserId')
            ->willReturn($collectionMock);

        $emitterMock = $this
            ->getMockBuilder(Emitter::class)
            ->getMock();

        $handler = new Feature(
            $repositoryMock,
            new FeatureValidator(),
            $emitterMock
        );

        $command         = new DeleteAll();
        $command->userId = 0;

        $this->assertSame($amount, $handler->handleDeleteAll($command));
    }

    public function testHandleUpdateOneInvalidProperties() {
        $repositoryMock = $this
            ->getMockBuilder(FeatureInterface::class)
            ->getMock();

        $emitterMock = $this
            ->getMockBuilder(Emitter::class)
            ->getMock();

        $handler = new Feature(
            $repositoryMock,
            new FeatureValidator(),
            $emitterMock
        );

        $this->expectedException('InvalidArgumentException');

        $command = $this
            ->getMockBuilder(UpdateOne::class)
            ->disableOriginalConstructor()
            ->getMock();

        $command->name   = 'Name';
        $command->value  = 'value';
        $command->userId = 1;

        $handler->handleUpdateOne($command);
    }

    public function testHandleUpdateOne() {
        $userId        = 1;
        $featureEntity = new FeatureEntity(['user_id' => $userId], $this->optimus);

        $dbConnectionMock = $this->getMockBuilder(ConnectionInterface::class)
            ->getMock();

        $entityFactory = new EntityFactory($this->optimus);
        $entityFactory->create('Profile\Feature');

        $featureRepository = $this->getMockBuilder(DBFeature::class)
            ->setMethods(['findByUserIdAndSlug', 'save'])
            ->setConstructorArgs([$entityFactory, $this->optimus, $dbConnectionMock])
            ->getMock();

        $featureRepository
            ->expects($this->once())
            ->method('findByUserIdAndSlug')
            ->willReturn($featureEntity);

        $featureRepository
            ->expects($this->once())
            ->method('save')
            ->will($this->returnValue($featureEntity));

        $emitterMock = $this
            ->getMockBuilder(Emitter::class)
            ->getMock();

        $handler = new Feature(
            $featureRepository,
            new FeatureValidator(),
            $emitterMock
        );

        $command              = new UpdateOne();
        $command->featureSlug = 'slug';
        $command->value       = 'cool-value';
        $command->userId      = $userId;

        $feature = $handler->handleUpdateOne($command);

        $this->assertInstanceOf(FeatureEntity::class, $feature);
        $this->assertSame($userId, $feature->user_id);
    }

    public function testHandleDeleteOneInvalidFeatureSlug() {
        $repositoryMock = $this
            ->getMockBuilder(FeatureInterface::class)
            ->getMock();

        $emitterMock = $this
            ->getMockBuilder(Emitter::class)
            ->getMock();

        $handler = new Feature(
            $repositoryMock,
            new FeatureValidator(),
            $emitterMock
        );

        $this->expectedException('InvalidArgumentException');

        $commandMock = $this
            ->getMockBuilder(DeleteOne::class)
            ->disableOriginalConstructor()
            ->getMock();

        $commandMock->userId      = 1;
        $commandMock->featureSlug = '';

        $handler->handleDeleteOne($commandMock);
    }

    public function testHandleDeleteOne() {
        $id            = 1;
        $userId        = 1;
        $featureSlug   = 'test-slug';
        $featureEntity = new FeatureEntity(['id' => $id, 'user_id' => $userId, 'slug' => $featureSlug], $this->optimus);

        $dbConnectionMock = $this->getMockBuilder(ConnectionInterface::class)
            ->getMock();

        $entityFactory = new EntityFactory($this->optimus);
        $entityFactory->create('Profile\Feature');

        $featureRepository = $this->getMockBuilder(DBFeature::class)
            ->setMethods(['findByUserIdAndSlug', 'delete'])
            ->setConstructorArgs([$entityFactory, $this->optimus, $dbConnectionMock])
            ->getMock();

        $featureRepository
            ->expects($this->once())
            ->method('findByUserIdAndSlug')
            ->willReturn($featureEntity);

        $amount = 1;
        $featureRepository
            ->expects($this->once())
            ->method('delete')
            ->willReturn($amount);

        $emitterMock = $this
            ->getMockBuilder(Emitter::class)
            ->getMock();

        $handler = new Feature(
            $featureRepository,
            new FeatureValidator(),
            $emitterMock
        );

        $commandMock              = new DeleteOne();
        $commandMock->userId      = $userId;
        $commandMock->featureSlug = $featureSlug;

        $this->assertSame($amount, $handler->handleDeleteOne($commandMock));
    }
}
