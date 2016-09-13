<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace Test\Unit\Controller;

use App\Command\Normalised\CreateNew;
use App\Command\Normalised\DeleteAll;
use App\Command\Normalised\DeleteOne;
use App\Command\Normalised\UpdateOne;
use App\Command\ResponseDispatch;
use App\Controller\Normalised;
use App\Entity\Normalised as NormalisedEntity;
use App\Entity\User as UserEntity;
use App\Factory\Command;
use App\Repository\DBNormalised;
use Illuminate\Support\Collection;
use Jenssegers\Optimus\Optimus;
use League\Tactician\CommandBus;
use Slim\Http\Request;
use Slim\Http\Response;
use Test\Unit\AbstractUnit;

class NormalisedTest extends AbstractUnit {
    private function getEntity($sourceId, $id) {
        $optimus = $this->getMockBuilder(Optimus::class)
            ->disableOriginalConstructor()
            ->getMock();

        return new NormalisedEntity(
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

    private function getUserEntity() {
        $optimus = $this->getMockBuilder(Optimus::class)
            ->disableOriginalConstructor()
            ->getMock();

        return new UserEntity(
            [
                'id'         => 1,
                'username'   => 'Username',
                'created_at' => time(),
                'updated_at' => time()
            ],
            $optimus
        );
    }

    public function testListAll() {
        $requestMock = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->setMethods(['getAttribute', 'getQueryParam'])
            ->getMock();
        $requestMock
            ->method('getAttribute')
            ->will($this->onConsecutiveCalls($this->getUserEntity(), 1));
        $requestMock
            ->method('getQueryParam')
            ->will($this->returnValue('mapped-1'));

        $responseMock = $this->getMockBuilder(Response::class)
            ->disableOriginalConstructor()
            ->getMock();

        $dbNormalisedMock = $this->getMockBuilder(DBNormalised::class)
            ->disableOriginalConstructor()
            ->setMethods(['getAllByUserIdSourceIdAndNames'])
            ->getMock();
        $dbNormalisedMock
            ->method('getAllByUserIdSourceIdAndNames')
            ->will(
                $this->returnValueMap(
                    [
                    [
                    1,
                    1,
                    ['mapped-1'],
                    new Collection([$this->getEntity(1, 1)])
                    ]
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

        $optimus = $this->getMockBuilder(Optimus::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mappedMock = $this->getMockBuilder(Normalised::class)
            ->setConstructorArgs([$dbNormalisedMock, $commandBus, $commandFactory, $optimus])
            ->setMethods(null)
            ->getMock();

        $this->assertEquals($responseMock, $mappedMock->listAll($requestMock, $responseMock));
    }

    public function testGetOne() {
        $requestMock = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->setMethods(['getAttribute'])
            ->getMock();
        $requestMock
            ->method('getAttribute')
            ->will($this->onConsecutiveCalls($this->getUserEntity(), 1, 'mapped-1'));

        $responseMock = $this->getMockBuilder(Response::class)
            ->disableOriginalConstructor()
            ->getMock();

        $dbNormalisedMock = $this->getMockBuilder(DBNormalised::class)
            ->disableOriginalConstructor()
            ->setMethods(['findOneByUserIdSourceIdAndName'])
            ->getMock();
        $dbNormalisedMock
            ->method('findOneByUserIdSourceIdAndName')
            ->will(
                $this->returnValueMap(
                    [
                    [
                    1,
                    1,
                    'mapped-1',
                    $this->getEntity(1, 1)
                    ]
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

        $optimus = $this->getMockBuilder(Optimus::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mappedMock = $this->getMockBuilder(Normalised::class)
            ->setConstructorArgs([$dbNormalisedMock, $commandBus, $commandFactory, $optimus])
            ->setMethods(null)
            ->getMock();

        $this->assertEquals($responseMock, $mappedMock->getOne($requestMock, $responseMock));
    }

    public function testCreateNew() {
        $requestMock = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->setMethods(['getAttribute', 'getParsedBody'])
            ->getMock();
        $requestMock
            ->expects($this->exactly(2))
            ->method('getAttribute')
            ->will(
                $this->onConsecutiveCalls($this->getUserEntity(), 1)
            );
        $requestMock
            ->expects($this->once())
            ->method('getParsedBody')
            ->will($this->returnValue(['request']));

        $responseMock = $this->getMockBuilder(Response::class)
            ->disableOriginalConstructor()
            ->getMock();

        $dbNormalisedMock = $this->getMockBuilder(DBNormalised::class)
            ->disableOriginalConstructor()
            ->getMock();

        $commandBus = $this->getMockBuilder(CommandBus::class)
            ->disableOriginalConstructor()
            ->setMethods(['handle'])
            ->getMock();
        $commandBus
            ->expects($this->exactly(2))
            ->method('handle')
            ->will($this->onConsecutiveCalls($this->getEntity(1, 1), $responseMock));

        $commandFactory = $this->getMockBuilder(Command::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $commandFactory
            ->expects($this->exactly(2))
            ->method('create')
            ->will($this->onConsecutiveCalls(new CreateNew(), new ResponseDispatch()));

        $optimus = $this->getMockBuilder(Optimus::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mappedMock = $this->getMockBuilder(Normalised::class)
            ->setConstructorArgs([$dbNormalisedMock, $commandBus, $commandFactory, $optimus])
            ->setMethods(null)
            ->getMock();

        $this->assertEquals($responseMock, $mappedMock->createNew($requestMock, $responseMock));
    }

    public function testDeleteAll() {
        $requestMock = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->setMethods(['getAttribute'])
            ->getMock();
        $requestMock
            ->method('getAttribute')
            ->will($this->onConsecutiveCalls($this->getUserEntity(), 1));

        $responseMock = $this->getMockBuilder(Response::class)
            ->disableOriginalConstructor()
            ->getMock();

        $dbNormalisedMock = $this->getMockBuilder(DBNormalised::class)
            ->disableOriginalConstructor()
            ->getMock();

        $commandBus = $this->getMockBuilder(CommandBus::class)
            ->disableOriginalConstructor()
            ->setMethods(['handle'])
            ->getMock();
        $commandBus
            ->expects($this->exactly(2))
            ->method('handle')
            ->will($this->onConsecutiveCalls($this->getEntity(1, 1), $responseMock));

        $commandFactory = $this->getMockBuilder(Command::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $commandFactory
            ->expects($this->exactly(2))
            ->method('create')
            ->will($this->onConsecutiveCalls(new DeleteAll(), new ResponseDispatch()));

        $optimus = $this->getMockBuilder(Optimus::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mappedMock = $this->getMockBuilder(Normalised::class)
            ->setConstructorArgs([$dbNormalisedMock, $commandBus, $commandFactory, $optimus])
            ->setMethods(null)
            ->getMock();

        $this->assertEquals($responseMock, $mappedMock->deleteAll($requestMock, $responseMock));
    }

    public function testDeleteOne() {
        $requestMock = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->setMethods(['getAttribute'])
            ->getMock();
        $requestMock
            ->method('getAttribute')
            ->will($this->onConsecutiveCalls($this->getUserEntity(), 1, 'mapped-1'));

        $responseMock = $this->getMockBuilder(Response::class)
            ->disableOriginalConstructor()
            ->getMock();

        $dbNormalisedMock = $this->getMockBuilder(DBNormalised::class)
            ->disableOriginalConstructor()
            ->getMock();

        $commandBus = $this->getMockBuilder(CommandBus::class)
            ->disableOriginalConstructor()
            ->setMethods(['handle'])
            ->getMock();
        $commandBus
            ->expects($this->exactly(2))
            ->method('handle')
            ->will($this->onConsecutiveCalls($this->getEntity(1, 1), $responseMock));

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

        $mappedMock = $this->getMockBuilder(Normalised::class)
            ->setConstructorArgs([$dbNormalisedMock, $commandBus, $commandFactory, $optimus])
            ->setMethods(null)
            ->getMock();

        $this->assertEquals($responseMock, $mappedMock->deleteOne($requestMock, $responseMock));
    }

    public function testUpdateOne() {
        $requestMock = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->setMethods(['getAttribute', 'getParsedBody'])
            ->getMock();
        $requestMock
            ->expects($this->exactly(3))
            ->method('getAttribute')
            ->will(
                $this->onConsecutiveCalls($this->getUserEntity(), 1, 'mapped-1')
            );
        $requestMock
            ->expects($this->once())
            ->method('getParsedBody')
            ->will($this->returnValue(['request']));

        $responseMock = $this->getMockBuilder(Response::class)
            ->disableOriginalConstructor()
            ->getMock();

        $dbNormalisedMock = $this->getMockBuilder(DBNormalised::class)
            ->disableOriginalConstructor()
            ->getMock();

        $commandBus = $this->getMockBuilder(CommandBus::class)
            ->disableOriginalConstructor()
            ->setMethods(['handle'])
            ->getMock();
        $commandBus
            ->expects($this->exactly(2))
            ->method('handle')
            ->will($this->onConsecutiveCalls($this->getEntity(1, 1), $responseMock));

        $commandFactory = $this->getMockBuilder(Command::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $commandFactory
            ->expects($this->exactly(2))
            ->method('create')
            ->will($this->onConsecutiveCalls(new UpdateOne(), new ResponseDispatch()));

        $optimus = $this->getMockBuilder(Optimus::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mappedMock = $this->getMockBuilder(Normalised::class)
            ->setConstructorArgs([$dbNormalisedMock, $commandBus, $commandFactory, $optimus])
            ->setMethods(null)
            ->getMock();

        $this->assertEquals($responseMock, $mappedMock->updateOne($requestMock, $responseMock));
    }
}
