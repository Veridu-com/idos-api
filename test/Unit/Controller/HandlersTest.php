<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace Test\Unit\Controller;

use App\Command\Handler\CreateNew;
use App\Command\Handler\DeleteAll;
use App\Command\Handler\DeleteOne;
use App\Command\Handler\UpdateOne;
use App\Command\ResponseDispatch;
use App\Controller\Handlers;
use App\Entity\Company;
use App\Entity\Handler as HandlerEntity;
use App\Factory\Command;
use App\Repository\DBHandler;
use Illuminate\Support\Collection;
use Jenssegers\Optimus\Optimus;
use League\Tactician\CommandBus;
use Slim\Http\Request;
use Slim\Http\Response;
use Test\Unit\AbstractUnit;

class HandlersTest extends AbstractUnit {
    private function getCompanyEntity() {
        $optimus = $this->getMockBuilder(Optimus::class)
            ->disableOriginalConstructor()
            ->getMock();

        return new Company(
            [
                'name'       => 'New Company',
                'id'         => 1,
                'slug'       => 'new-company',
                'created_at' => time(),
                'updated_at' => time()
            ],
            $optimus
        );
    }

    private function getEntity($id) {
        $optimus = $this->getMockBuilder(Optimus::class)
            ->disableOriginalConstructor()
            ->getMock();

        return new HandlerEntity(
            [
                'id'         => $id,
                'name'       => 'New Handler',
                'url'        => 'http://localhost:8080',
                'created_at' => time(),
                'updated_at' => time()
            ],
            $optimus
        );
    }

    public function testListAll() {
        $requestMock = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->setMethods(['getAttribute'])
            ->getMock();
        $requestMock
            ->method('getAttribute')
            ->will($this->returnValue($this->getCompanyEntity()));

        $responseMock = $this->getMockBuilder(Response::class)
            ->disableOriginalConstructor()
            ->getMock();

        $dbHandlerMock = $this->getMockBuilder(DBHandler::class)
            ->disableOriginalConstructor()
            ->setMethods(['getAllByCompany'])
            ->getMock();

        $dbHandlerMock
            ->method('getAllByCompany')
            ->will($this->returnValue(new Collection([$this->getEntity(1), $this->getEntity(2)])));

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

        $serviceMock = $this->getMockBuilder(Handlers::class)
            ->setConstructorArgs([$dbHandlerMock, $commandBus, $commandFactory])
            ->setMethods(null)
            ->getMock();

        // assertEquals: we want the array key => value combinations to be the same, but not necessarily in the same order
        $this->assertEquals($responseMock, $serviceMock->listAll($requestMock, $responseMock));
    }

    public function testGetOne() {
        $requestMock = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->setMethods(['getAttribute'])
            ->getMock();
        $requestMock
            ->expects($this->exactly(2))
            ->method('getAttribute')
            ->will(
                $this->onConsecutiveCalls(
                    $this->getCompanyEntity(),
                    'email',
                    'new-service'
                )
            );

        $responseMock = $this->getMockBuilder(Response::class)
            ->disableOriginalConstructor()
            ->getMock();

        $dbHandlerMock = $this->getMockBuilder(DBHandler::class)
            ->disableOriginalConstructor()
            ->setMethods(['findOne'])
            ->getMock();
        $dbHandlerMock
            ->expects($this->once())
            ->method('findOne')
            ->will($this->returnValue($this->getEntity(1)));

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

        $serviceMock = $this->getMockBuilder(Handlers::class)
            ->setConstructorArgs([$dbHandlerMock, $commandBus, $commandFactory])
            ->setMethods(null)
            ->getMock();

        // assertEquals: we want the array key => value combinations to be the same, but not necessarily in the same order
        $this->assertEquals($responseMock, $serviceMock->getOne($requestMock, $responseMock));
    }

    public function testCreateNew() {
        $requestMock = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->setMethods(['getAttribute', 'getParsedBody'])
            ->getMock();
        $requestMock
            ->expects($this->once())
            ->method('getAttribute')
            ->will(
                $this->returnValue($this->getEntity(1))
            );
        $requestMock
            ->expects($this->once())
            ->method('getParsedBody')
            ->will($this->returnValue(['request']));

        $responseMock = $this->getMockBuilder(Response::class)
            ->disableOriginalConstructor()
            ->getMock();

        $dbHandlerMock = $this->getMockBuilder(DBHandler::class)
            ->disableOriginalConstructor()
            ->getMock();

        $commandBus = $this->getMockBuilder(CommandBus::class)
            ->disableOriginalConstructor()
            ->setMethods(['handle'])
            ->getMock();
        $commandBus
            ->expects($this->exactly(2))
            ->method('handle')
            ->will($this->onConsecutiveCalls($this->getEntity(1), $responseMock));

        $commandFactory = $this->getMockBuilder(Command::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $commandFactory
            ->expects($this->exactly(2))
            ->method('create')
            ->will($this->onConsecutiveCalls(new CreateNew(), new ResponseDispatch()));

        $serviceMock = $this->getMockBuilder(Handlers::class)
            ->setConstructorArgs([$dbHandlerMock, $commandBus, $commandFactory])
            ->setMethods(null)
            ->getMock();

        // assertEquals: we want the array key => value combinations to be the same, but not necessarily in the same order
        $this->assertEquals($responseMock, $serviceMock->createNew($requestMock, $responseMock));
    }

    public function testDeleteAll() {
        $requestMock = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->setMethods(['getAttribute'])
            ->getMock();
        $requestMock
            ->expects($this->once())
            ->method('getAttribute')
            ->will($this->returnValue($this->getEntity(1)));

        $responseMock = $this->getMockBuilder(Response::class)
            ->disableOriginalConstructor()
            ->getMock();

        $dbHandlerMock = $this->getMockBuilder(DBHandler::class)
            ->disableOriginalConstructor()
            ->getMock();

        $commandBus = $this->getMockBuilder(CommandBus::class)
            ->disableOriginalConstructor()
            ->setMethods(['handle'])
            ->getMock();
        $commandBus
            ->expects($this->exactly(2))
            ->method('handle')
            ->will($this->onConsecutiveCalls($this->getEntity(1), $responseMock));

        $commandFactory = $this->getMockBuilder(Command::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $commandFactory
            ->expects($this->exactly(2))
            ->method('create')
            ->will($this->onConsecutiveCalls(new DeleteAll(), new ResponseDispatch()));

        $serviceMock = $this->getMockBuilder(Handlers::class)
            ->setConstructorArgs([$dbHandlerMock, $commandBus, $commandFactory])
            ->setMethods(null)
            ->getMock();

        // assertEquals: we want the array key => value combinations to be the same, but not necessarily in the same order
        $this->assertEquals($responseMock, $serviceMock->deleteAll($requestMock, $responseMock));
    }

    public function testDeleteOne() {
        $requestMock = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->setMethods(['getAttribute'])
            ->getMock();
        $requestMock
            ->expects($this->exactly(2))
            ->method('getAttribute')
            ->will(
                $this->onConsecutiveCalls(
                    $this->getCompanyEntity(),
                    'email',
                    'new-service'
                )
            );

        $responseMock = $this->getMockBuilder(Response::class)
            ->disableOriginalConstructor()
            ->getMock();

        $dbHandlerMock = $this->getMockBuilder(DBHandler::class)
            ->disableOriginalConstructor()
            ->getMock();

        $commandBus = $this->getMockBuilder(CommandBus::class)
            ->disableOriginalConstructor()
            ->setMethods(['handle'])
            ->getMock();
        $commandBus
            ->expects($this->exactly(2))
            ->method('handle')
            ->will($this->onConsecutiveCalls($this->getEntity(0), $responseMock));

        $commandFactory = $this->getMockBuilder(Command::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $commandFactory
            ->expects($this->exactly(2))
            ->method('create')
            ->will($this->onConsecutiveCalls(new DeleteOne(), new ResponseDispatch()));

        $serviceMock = $this->getMockBuilder(Handlers::class)
            ->setConstructorArgs([$dbHandlerMock, $commandBus, $commandFactory])
            ->setMethods(null)
            ->getMock();

        // assertEquals: we want the array key => value combinations to be the same, but not necessarily in the same order
        $this->assertEquals($responseMock, $serviceMock->deleteOne($requestMock, $responseMock));
    }

    public function testUpdateOne() {
        $requestMock = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->setMethods(['getAttribute', 'getParsedBody'])
            ->getMock();
        $requestMock
            ->expects($this->exactly(2))
            ->method('getAttribute')
            ->will(
                $this->onConsecutiveCalls(
                    $this->getCompanyEntity(),
                    'email',
                    'new-service'
                )
            );
        $requestMock
            ->expects($this->once())
            ->method('getParsedBody')
            ->will($this->returnValue(['request']));

        $responseMock = $this->getMockBuilder(Response::class)
            ->disableOriginalConstructor()
            ->getMock();

        $dbHandlerMock = $this->getMockBuilder(DBHandler::class)
            ->disableOriginalConstructor()
            ->getMock();

        $commandBus = $this->getMockBuilder(CommandBus::class)
            ->disableOriginalConstructor()
            ->setMethods(['handle'])
            ->getMock();
        $commandBus
            ->expects($this->exactly(2))
            ->method('handle')
            ->will($this->onConsecutiveCalls($this->getEntity(0), $responseMock));

        $commandFactory = $this->getMockBuilder(Command::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $commandFactory
            ->expects($this->exactly(2))
            ->method('create')
            ->will($this->onConsecutiveCalls(new UpdateOne(), new ResponseDispatch()));

        $serviceMock = $this->getMockBuilder(Handlers::class)
            ->setConstructorArgs([$dbHandlerMock, $commandBus, $commandFactory])
            ->setMethods(null)
            ->getMock();

        // assertEquals: we want the array key => value combinations to be the same, but not necessarily in the same order
        $this->assertEquals($responseMock, $serviceMock->updateOne($requestMock, $responseMock));
    }
}
