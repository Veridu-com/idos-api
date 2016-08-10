<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace Test\Unit\Controller;

use App\Command\ResponseDispatch;
use App\Controller\Services;
use App\Entity\Service as ServiceEntity;
use App\Factory\Command;
use App\Repository\DBService;
use Illuminate\Support\Collection;
use Jenssegers\Optimus\Optimus;
use League\Tactician\CommandBus;
use Slim\Http\Request;
use Slim\Http\Response;
use Test\Unit\AbstractUnit;

class ServicesTest extends AbstractUnit {
    private function getEntity($id) {
        $optimus = $this->getMockBuilder(Optimus::class)
            ->disableOriginalConstructor()
            ->getMock();

        return new ServiceEntity(
            [
                'name'       => 'New Service',
                'id'         => $id,
                'slug'       => 'new-service',
                'enabled'    => true,
                'created_at' => time(),
                'updated_at' => time()
            ],
            $optimus
        );
    }

    public function testListAll() {
        $requestMock = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->setMethods(null)
            ->getMock();

        $responseMock = $this->getMockBuilder(Response::class)
            ->disableOriginalConstructor()
            ->getMock();

        $dbServiceMock = $this->getMockBuilder(DBService::class)
            ->disableOriginalConstructor()
            ->setMethods(['getAll'])
            ->getMock();
        $dbServiceMock
            ->method('getAll')
            ->will($this->returnValue(new Collection($this->getEntity(1))));

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

        $serviceMock = $this->getMockBuilder(Services::class)
            ->setConstructorArgs([$dbServiceMock, $commandBus, $commandFactory])
            ->setMethods(null)
            ->getMock();

        $this->assertSame($responseMock, $serviceMock->listAll($requestMock, $responseMock));
    }
}
