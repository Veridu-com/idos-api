<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace Test\Unit\Controller;

use App\Command\ResponseDispatch;
use App\Command\Setting\CreateNew;
use App\Command\Setting\DeleteAll;
use App\Command\Setting\DeleteOne;
use App\Command\Setting\UpdateOne;
use App\Controller\Settings;
use App\Entity\Company;
use App\Entity\Setting as SettingEntity;
use App\Factory\Command;
use App\Repository\DBSetting;
use Illuminate\Support\Collection;
use Jenssegers\Optimus\Optimus;
use League\Tactician\CommandBus;
use Slim\Http\Request;
use Slim\Http\Response;
use Test\Unit\AbstractUnit;

class SettingsTest extends AbstractUnit {
    public function testListAll() {
        $optimus = $this->getMockBuilder(Optimus::class)
            ->disableOriginalConstructor()
            ->getMock();

        $requestMock = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->setMethods(['getAttribute'])
            ->getMock();
        $requestMock
            ->method('getAttribute')
            ->will(
                $this->returnValue(
                    new Company(
                        ['id' => 1],
                        $optimus
                    )
                )
            );

        $responseMock = $this->getMockBuilder(Response::class)
            ->disableOriginalConstructor()
            ->getMock();

        $repositoryMock = $this->getMockBuilder(DBSetting::class)
            ->disableOriginalConstructor()
            ->setMethods(['getAllByCompanyId'])
            ->getMock();
        $repositoryMock
            ->method('getAllByCompanyId')
            ->will(
                $this->returnValue(
                    [
                        'pagination' => 'pagination',
                        'collection' => new Collection(
                            new SettingEntity(
                                [
                                        'section'    => 'section',
                                        'updated_at' => time()
                                    ],
                                $optimus
                            )
                        )
                    ]
                )
            );

        $commandBus = $this->getMockBuilder(CommandBus::class)
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();
        $commandBus
            ->expects($this->once())
            ->method('handle')
            ->will($this->returnValue($responseMock));

        $commandFactory = $this->getMockBuilder(Command::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $commandFactory
            ->expects($this->once())
            ->method('create')
            ->will($this->returnValue(new ResponseDispatch()));

        $settingsMock = $this->getMockBuilder(Settings::class)
            ->setConstructorArgs([$repositoryMock, $commandBus, $commandFactory, $optimus])
            ->setMethods(null)
            ->getMock();

        // assertEquals: we want the array key => value combinations to be the same, but not necessarily in the same order
        $this->assertEquals($responseMock, $settingsMock->listAll($requestMock, $responseMock));
    }

    public function testGetOne() {
        $optimus = $this->getMockBuilder(Optimus::class)
            ->disableOriginalConstructor()
            ->getMock();
        $requestMock = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->setMethods(['getAttribute'])
            ->getMock();
        $requestMock
            ->expects($this->exactly(1))
            ->method('getAttribute')
            ->will(
                $this->onConsecutiveCalls(
                    $this->returnValue(
                        '1'
                    ),
                    'section',
                    'propName'
                )
            );

        $responseMock = $this->getMockBuilder(Response::class)
            ->disableOriginalConstructor()
            ->getMock();

        $repositoryMock = $this->getMockBuilder(DBSetting::class)
            ->disableOriginalConstructor()
            ->setMethods(['find'])
            ->getMock();
        $repositoryMock
            ->method('find')
            ->will(
                $this->returnValue(
                    new SettingEntity(
                        [
                            'section'    => 'section',
                            'property'   => 'property',
                            'value'      => 'value',
                            'created_at' => time(),
                            'updated_at' => time()
                        ],
                        $optimus
                    )
                )
            );

        $commandBus = $this->getMockBuilder(CommandBus::class)
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();
        $commandBus
            ->expects($this->once())
            ->method('handle')
            ->will($this->returnValue($responseMock));

        $commandFactory = $this->getMockBuilder(Command::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $commandFactory
            ->expects($this->once())
            ->method('create')
            ->will($this->returnValue(new ResponseDispatch()));
        $settingsMock = $this->getMockBuilder(Settings::class)
            ->setConstructorArgs([$repositoryMock, $commandBus, $commandFactory, $optimus])
            ->setMethods(null)
            ->getMock();

        // assertEquals: we want the array key => value combinations to be the same, but not necessarily in the same order
        $this->assertEquals($responseMock, $settingsMock->getOne($requestMock, $responseMock));
    }

    public function testCreateNew() {
        $optimus = $this->getMockBuilder(Optimus::class)
            ->disableOriginalConstructor()
            ->getMock();
        $requestMock = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->setMethods(['getAttribute', 'getParsedBody'])
            ->getMock();
        $requestMock
            ->method('getAttribute')
            ->will(
                $this->returnValue(
                    new Company(
                        ['id' => 1], $optimus
                    )
                )
            );
        $requestMock
            ->method('getParsedBody')
            ->will($this->returnValue(['request' => 'request']));

        $responseMock = $this->getMockBuilder(Response::class)
            ->disableOriginalConstructor()
            ->getMock();

        $repositoryMock = $this->getMockBuilder(DBSetting::class)
            ->disableOriginalConstructor()
            ->setMethods(['getAllByCompanyId'])
            ->getMock();
        $repositoryMock
            ->method('getAllByCompanyId')
            ->will(
                $this->returnValue(
                    new Collection(
                        [
                            'section'    => 'section',
                            'updated_at' => time()
                        ]
                    )
                )
            );

        $commandBus = $this->getMockBuilder(CommandBus::class)
            ->disableOriginalConstructor()
            ->setMethods(['handle'])
            ->getMock();
        $commandBus
            ->expects($this->exactly(2))
            ->method('handle')
            ->will(
                $this->onConsecutiveCalls(
                    new SettingEntity(
                        [
                            'section'    => 'section',
                            'property'   => 'property',
                            'value'      => 'value',
                            'created_at' => time(),
                            'updated_at' => time()
                        ],
                        $optimus
                    ),
                    $responseMock
                )
            );

        $commandFactory = $this->getMockBuilder(Command::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $commandFactory
            ->expects($this->exactly(2))
            ->method('create')
            ->will($this->onConsecutiveCalls(new CreateNew(), new ResponseDispatch()));

        $settingsMock = $this->getMockBuilder(Settings::class)
            ->setConstructorArgs([$repositoryMock, $commandBus, $commandFactory, $optimus])
            ->setMethods(null)
            ->getMock();

        // assertEquals: we want the array key => value combinations to be the same, but not necessarily in the same order
        $this->assertEquals($responseMock, $settingsMock->createNew($requestMock, $responseMock));
    }

    public function testDeleteAll() {
        $optimus = $this->getMockBuilder(Optimus::class)
            ->disableOriginalConstructor()
            ->getMock();
        $requestMock = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->setMethods(['getAttribute'])
            ->getMock();
        $requestMock
            ->method('getAttribute')
            ->will(
                $this->returnValue(
                    new Company(
                        ['id' => 1], $optimus
                    )
                )
            );

        $responseMock = $this->getMockBuilder(Response::class)
            ->disableOriginalConstructor()
            ->getMock();

        $repositoryMock = $this->getMockBuilder(DBSetting::class)
            ->disableOriginalConstructor()
            ->setMethods(['getAllByCompanyId'])
            ->getMock();
        $repositoryMock
            ->method('getAllByCompanyId')
            ->will(
                $this->returnValue(
                    new Collection(
                        [
                            'section'    => 'section',
                            'updated_at' => time()
                        ]
                    )
                )
            );

        $commandBus = $this->getMockBuilder(CommandBus::class)
            ->disableOriginalConstructor()
            ->setMethods(['handle'])
            ->getMock();
        $commandBus
            ->expects($this->exactly(2))
            ->method('handle')
            ->will(
                $this->onConsecutiveCalls(7, $responseMock)
            );

        $commandFactory = $this->getMockBuilder(Command::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $commandFactory
            ->expects($this->exactly(2))
            ->method('create')
            ->will($this->onConsecutiveCalls(new DeleteAll(), new ResponseDispatch()));

        $settingsMock = $this->getMockBuilder(Settings::class)
            ->setConstructorArgs([$repositoryMock, $commandBus, $commandFactory, $optimus])
            ->setMethods(null)
            ->getMock();

        // assertEquals: we want the array key => value combinations to be the same, but not necessarily in the same order
        $this->assertEquals($responseMock, $settingsMock->deleteAll($requestMock, $responseMock));
    }

    public function testDeleteOne() {
        $optimus = $this->getMockBuilder(Optimus::class)
            ->disableOriginalConstructor()
            ->getMock();
        $requestMock = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->setMethods(['getAttribute'])
            ->getMock();
        $requestMock
            ->expects($this->exactly(1))
            ->method('getAttribute')
            ->will(
                $this->onConsecutiveCalls(
                    $this->returnValue(
                        '1'
                    ),
                    'section',
                    'propName'
                )
            );

        $repositoryMock = $this->getMockBuilder(DBSetting::class)
            ->disableOriginalConstructor()
            ->setMethods(null)
            ->getMock();

        $responseMock = $this->getMockBuilder(Response::class)
            ->disableOriginalConstructor()
            ->getMock();

        $commandBus = $this->getMockBuilder(CommandBus::class)
            ->disableOriginalConstructor()
            ->setMethods(['handle'])
            ->getMock();
        $commandBus
            ->expects($this->exactly(2))
            ->method('handle')
            ->will(
                $this->onConsecutiveCalls(1, $responseMock)
            );

        $commandFactory = $this->getMockBuilder(Command::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $commandFactory
            ->expects($this->exactly(2))
            ->method('create')
            ->will($this->onConsecutiveCalls(new DeleteOne(), new ResponseDispatch()));

        $optimus = $this->getMockBuilder(Optimus::class)
            ->disableOriginalConstructor()
            ->getMock();

        $settingsMock = $this->getMockBuilder(Settings::class)
            ->setConstructorArgs([$repositoryMock, $commandBus, $commandFactory, $optimus])
            ->setMethods(null)
            ->getMock();

        // assertEquals: we want the array key => value combinations to be the same, but not necessarily in the same order
        $this->assertEquals($responseMock, $settingsMock->deleteOne($requestMock, $responseMock));
    }

    public function testUpdateOne() {
        $optimus = $this->getMockBuilder(Optimus::class)
            ->disableOriginalConstructor()
            ->getMock();
        $requestMock = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->setMethods(['getAttribute', 'getParsedBody'])
            ->getMock();
        $requestMock
            ->expects($this->exactly(1))
            ->method('getAttribute')
            ->will(
                $this->onConsecutiveCalls(
                    new Company(
                        ['id' => 1], $optimus
                    ),
                    'section',
                    'property'
                )
            );

        $requestMock
            ->method('getParsedBody')
            ->will($this->returnValue(['request' => 'request']));

        $responseMock = $this->getMockBuilder(Response::class)
            ->disableOriginalConstructor()
            ->getMock();

        $repositoryMock = $this->getMockBuilder(DBSetting::class)
            ->disableOriginalConstructor()
            ->setMethods(['getAllByCompanyId'])
            ->getMock();
        $repositoryMock
            ->method('getAllByCompanyId')
            ->will(
                $this->returnValue(
                    new Collection(
                        [
                            'section'    => 'section',
                            'updated_at' => time()
                        ]
                    )
                )
            );

        $commandBus = $this->getMockBuilder(CommandBus::class)
            ->disableOriginalConstructor()
            ->setMethods(['handle'])
            ->getMock();
        $commandBus
            ->expects($this->exactly(2))
            ->method('handle')
            ->will(
                $this->onConsecutiveCalls(
                    new SettingEntity(
                        [
                            'section'    => 'section',
                            'property'   => 'property',
                            'value'      => 'value',
                            'created_at' => time(),
                            'updated_at' => time()
                        ],
                        $optimus
                    ),
                    $responseMock
                )
            );

        $commandFactory = $this->getMockBuilder(Command::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $commandFactory
            ->expects($this->exactly(2))
            ->method('create')
            ->will($this->onConsecutiveCalls(new UpdateOne(), new ResponseDispatch()));

        $settingsMock = $this->getMockBuilder(Settings::class)
            ->setConstructorArgs([$repositoryMock, $commandBus, $commandFactory, $optimus])
            ->setMethods(null)
            ->getMock();

        // assertEquals: we want the array key => value combinations to be the same, but not necessarily in the same order
        $this->assertEquals($responseMock, $settingsMock->updateOne($requestMock, $responseMock));
    }
}
