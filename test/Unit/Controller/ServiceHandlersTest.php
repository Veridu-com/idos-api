<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace Test\Unit\Controller;

use App\Command\ServiceHandler\CreateNew;
use App\Command\ServiceHandler\DeleteAll;
use App\Command\ServiceHandler\DeleteOne;
use App\Command\ServiceHandler\UpdateOne;
use App\Command\ResponseDispatch;
use App\Controller\ServiceHandlers;
use App\Entity\Company;
use App\Entity\ServiceHandler as ServiceHandlerEntity;
use App\Factory\Command;
use App\Repository\DBServiceHandler;
use Illuminate\Support\Collection;
use Jenssegers\Optimus\Optimus;
use League\Tactician\CommandBus;
use Slim\Http\Request;
use Slim\Http\Response;
use Test\Unit\AbstractUnit;

class ServiceHandlersTest extends AbstractUnit {
    private function getCompanyEntity() {
        return new Company(
            [
                'name'       => 'New Company',
                'id'         => 1,
                'slug'       => 'new-company',
                'created_at' => time(),
                'updated_at' => time()
            ]
        );
    }

    private function getEntity($id) {
        return new ServiceHandlerEntity(
            [
                'name'       => 'New Service Handler',
                'slug' => 'new-service-handler',
                'id'         => $id,
                'source' => 'source',
                'service_slug' => 'email',
                'location' => 'http://localhost:8080',
                'created_at' => time(),
                'updated_at' => time()
            ]
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

        $dbServiceHandlerMock = $this->getMockBuilder(DBServiceHandler::class)
            ->disableOriginalConstructor()
            ->setMethods(['getAllByCompanyId'])
            ->getMock();
        $dbServiceHandlerMock
            ->method('getAllByCompanyId')
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

        $serviceHandlerMock = $this->getMockBuilder(ServiceHandlers::class)
            ->setConstructorArgs([$dbServiceHandlerMock, $commandBus, $commandFactory])
            ->setMethods(null)
            ->getMock();

        $this->assertSame($responseMock, $serviceHandlerMock->listAll($requestMock, $responseMock));
    }

    public function testGetOne() {
        $requestMock = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->setMethods(['getAttribute'])
            ->getMock();
        $requestMock
            ->expects($this->exactly(3))
            ->method('getAttribute')
            ->will(
                $this->onConsecutiveCalls(
                    $this->getCompanyEntity(),
                    'email',
                    'new-service-handler'
                )
            );

        $responseMock = $this->getMockBuilder(Response::class)
            ->disableOriginalConstructor()
            ->getMock();

        $dbServiceHandlerMock = $this->getMockBuilder(DBServiceHandler::class)
            ->disableOriginalConstructor()
            ->setMethods(['findOne'])
            ->getMock();
        $dbServiceHandlerMock
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
            ->will($this->returnValue(new ResponseDispatch));

        $serviceHandlerMock = $this->getMockBuilder(ServiceHandlers::class)
            ->setConstructorArgs([$dbServiceHandlerMock, $commandBus, $commandFactory])
            ->setMethods(null)
            ->getMock();

        $this->assertSame($responseMock, $serviceHandlerMock->getOne($requestMock, $responseMock));
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

        $dbServiceHandlerMock = $this->getMockBuilder(DBServiceHandler::class)
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

        $serviceHandlerMock = $this->getMockBuilder(ServiceHandlers::class)
            ->setConstructorArgs([$dbServiceHandlerMock, $commandBus, $commandFactory])
            ->setMethods(null)
            ->getMock();

        $this->assertSame($responseMock, $serviceHandlerMock->createNew($requestMock, $responseMock));
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

        $dbServiceHandlerMock = $this->getMockBuilder(DBServiceHandler::class)
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

        $serviceHandlerMock = $this->getMockBuilder(ServiceHandlers::class)
            ->setConstructorArgs([$dbServiceHandlerMock, $commandBus, $commandFactory])
            ->setMethods(null)
            ->getMock();

        $this->assertSame($responseMock, $serviceHandlerMock->deleteAll($requestMock, $responseMock));
    }

    public function testDeleteOne() {
        $requestMock = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->setMethods(['getAttribute'])
            ->getMock();
        $requestMock
            ->expects($this->exactly(3))
            ->method('getAttribute')
            ->will(
                $this->onConsecutiveCalls(
                    $this->getCompanyEntity(),
                    'email',
                    'new-service-handler'
                )
            );

        $responseMock = $this->getMockBuilder(Response::class)
            ->disableOriginalConstructor()
            ->getMock();

        $dbServiceHandlerMock = $this->getMockBuilder(DBServiceHandler::class)
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

        $serviceHandlerMock = $this->getMockBuilder(ServiceHandlers::class)
            ->setConstructorArgs([$dbServiceHandlerMock, $commandBus, $commandFactory])
            ->setMethods(null)
            ->getMock();

        $this->assertSame($responseMock, $serviceHandlerMock->deleteOne($requestMock, $responseMock));
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
                $this->onConsecutiveCalls(
                    $this->getCompanyEntity(),
                    'email',
                    'new-service-handler'
                )
            );
        $requestMock
            ->expects($this->once())
            ->method('getParsedBody')
            ->will($this->returnValue(['request']));

        $responseMock = $this->getMockBuilder(Response::class)
            ->disableOriginalConstructor()
            ->getMock();

        $dbServiceHandlerMock = $this->getMockBuilder(DBServiceHandler::class)
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

        $serviceHandlerMock = $this->getMockBuilder(ServiceHandlers::class)
            ->setConstructorArgs([$dbServiceHandlerMock, $commandBus, $commandFactory])
            ->setMethods(null)
            ->getMock();

        $this->assertSame($responseMock, $serviceHandlerMock->updateOne($requestMock, $responseMock));
    }
}
