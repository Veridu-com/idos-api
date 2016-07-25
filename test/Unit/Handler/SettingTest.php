<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace Test\Unit\Handler;

use App\Command\Setting\CreateNew;
use App\Command\Setting\DeleteAll;
use App\Command\Setting\DeleteOne;
use App\Command\Setting\UpdateOne;
use App\Factory\Entity as EntityFactory;
use App\Factory\Repository;
use App\Factory\Validator;
use App\Handler\Setting;
use App\Repository\DBSetting;
use App\Repository\SettingInterface;
use App\Validator\Setting as SettingValidator;
use Slim\Container;
use Test\Unit\AbstractUnit;

class SettingTest extends AbstractUnit {
    public function testConstructCorrectInterface() {
        $repositoryMock = $this
            ->getMockBuilder(SettingInterface::class)
            ->getMock();
        $validatorMock = $this
            ->getMockBuilder(SettingValidator::class)
            ->getMock();

        $this->assertInstanceOf(
            'App\\Handler\\HandlerInterface',
            new Setting(
                $repositoryMock,
                $validatorMock
            )
        );
    }

    public function testRegister() {
        $container = new Container();

        $repositoryMock = $this
            ->getMockBuilder(SettingInterface::class)
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
            ->getMockBuilder(SettingValidator::class)
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

        Setting::register($container);
        $this->assertInstanceOf(Setting::class, $container[Setting::class]);
    }

    public function testHandleCreateNewInvalidSettingProperties() {
        $repositoryMock = $this
            ->getMockBuilder(SettingInterface::class)
            ->getMock();

        $handler = new Setting(
            $repositoryMock,
            new SettingValidator()
        );
        $this->setExpectedException('InvalidArgumentException');

        $commandMock = $this
            ->getMockBuilder(CreateNew::class)
            ->getMock();
        $commandMock->setParameters(
            [
                'section'    => 'section',
                'property'   => 'property',
                'value'      => '',
                'company_id' => 1
            ]
        );

        $handler->handleCreateNew($commandMock);
    }

    public function testHandleCreateNew() {
        $dbConnectionMock = $this->createMock('Illuminate\Database\ConnectionInterface');

        $entityFactory = new EntityFactory();
        $entityFactory->create('Setting');

        $SettingRepository = $this->getMockBuilder(DBSetting::class)
            ->setMethods(['save'])
            ->setConstructorArgs([$entityFactory, $dbConnectionMock])
            ->getMock();
        $SettingRepository
            ->expects($this->once())
            ->method('save')
            ->willReturn(true);

        $handler = new Setting(
            $SettingRepository,
            new SettingValidator()
        );

        $command              = new CreateNew();
        $command->section     = 'section';
        $command->property    = 'property';
        $command->value       = 'value';
        $command->companyId   = 1;

        $result = $handler->handleCreateNew($command);
        $this->assertSame('section', $result->section);
        $this->assertSame('property', $result->property);
        $this->assertSame('value', $result->value);
        $this->assertSame(1, $result->companyId);
    }

    public function testHandleDeleteAllInvalidCompanyId() {
        $repositoryMock = $this
            ->getMockBuilder(SettingInterface::class)
            ->getMock();

        $handler = new Setting(
            $repositoryMock,
            new SettingValidator()
        );

        $this->setExpectedException('InvalidArgumentException');

        $commandMock = $this
            ->getMockBuilder(DeleteAll::class)
            ->disableOriginalConstructor()
            ->getMock();

        $commandMock->companyId = '';

        $handler->handleDeleteAll($commandMock);
    }

    public function testHandleUpdateOneInvalidProperties() {
        $repositoryMock = $this
            ->getMockBuilder(SettingInterface::class)
            ->getMock();

        $handler = new Setting(
            $repositoryMock,
            new SettingValidator()
        );

        $this->setExpectedException('InvalidArgumentException');

        $commandMock = $this
            ->getMockBuilder(UpdateOne::class)
            ->disableOriginalConstructor()
            ->getMock();

        $commandMock->section   = 'section';
        $commandMock->property  = 'property';
        $commandMock->value     = '';
        $commandMock->companyId = 1;

        $handler->handleUpdateOne($commandMock);

    }

    public function testHandleDeleteOneInvalidSettingSlug() {
        $repositoryMock = $this
            ->getMockBuilder(SettingInterface::class)
            ->getMock();

        $handler = new Setting(
            $repositoryMock,
            new SettingValidator()
        );

        $this->setExpectedException('InvalidArgumentException');

        $commandMock = $this
            ->getMockBuilder(DeleteOne::class)
            ->disableOriginalConstructor()
            ->getMock();

        $commandMock->companyId = 1;
        $commandMock->section   = '';
        $commandMock->property  = 'property';

        $handler->handleDeleteOne($commandMock);
    }
}
