<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace Test\Unit\Repository;

use App\Entity\ServiceHandler as ServiceHandlerEntity;
use App\Exception\NotFound;
use App\Factory\Entity;
use App\Repository\DBServiceHandler;
use Illuminate\Database\Connection;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Jenssegers\Optimus\Optimus;
use Test\Unit\AbstractUnit;

class DBServiceHandlerTest extends AbstractUnit {
     /*
     * Jenssengers\Optimus\Optimus $optimus
     */
    private $optimus;

    public function setUp() {
        $this->optimus = $this->getMockBuilder(Optimus::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

     private function getEntity($id) {
        return new ServiceHandlerEntity(
            [
                'id' => 1,
                'name'         => 'New Service Handler',
                'slug'         => 'new-service-handler',
                'id'           => $id,
                'source'       => 'source',
                'service_slug' => 'email',
                'location'     => 'http://localhost:8080',
                'created_at'   => time(),
                'updated_at'   => time()
            ],
            $this->optimus
        );
    }

    private function getToArray() {
        return [
            'name'         => 'New Service Handler',
            'slug'         => 'new-service-handler',
            'source'       => 'source',
            'location'     => 'http://localhost:8080',
            'service_slug' => 'email',
            'created_at'   => time(),
            'updated_at'   => time()
        ];
    }

    public function testFindOneNotFound() {
        $factory = new Entity($this->optimus);
        $factory->create('ServiceHandler', []);
        $queryMock = $this->getMockBuilder(Builder::class)
            ->disableOriginalConstructor()
            ->setMethods(['where', 'get', 'first'])
            ->getMock();
        $queryMock
            ->method('where')
            ->will($this->returnValue($queryMock));
        $queryMock
            ->method('get')
            ->will($this->returnValue(new Collection([])));
        $queryMock
            ->method('first')
            ->will($this->returnValue(null));

        $connectionMock = $this->getMockBuilder(Connection::class)
            ->disableOriginalConstructor()
            ->setMethods(['setFetchMode', 'table'])
            ->getMock();
        $connectionMock
            ->method('setFetchMode')
            ->will($this->returnValue(1));
        $connectionMock
            ->method('table')
            ->will($this->returnValue($queryMock));
        $dbServiceHandler = new DBServiceHandler($factory, $this->optimus, $connectionMock);
        $this->setExpectedException(NotFound::class);
        $dbServiceHandler->findOne(0, 'slug', 'email');
    }

    public function testFindOne() {
        $factory = new Entity($this->optimus);
        $factory->create('ServiceHandler', []);
        $queryMock = $this->getMockBuilder(Builder::class)
            ->disableOriginalConstructor()
            ->setMethods(['where', 'get', 'first'])
            ->getMock();
        $queryMock
            ->method('where')
            ->will($this->returnValue($queryMock));
        $queryMock
            ->method('get')
            ->will($this->returnValue([$this->getEntity(1)]));
         $queryMock
            ->method('first')
            ->will($this->returnValue($this->getEntity(1)));
        $connectionMock = $this->getMockBuilder(Connection::class)
            ->disableOriginalConstructor()
            ->setMethods(['setFetchMode', 'table'])
            ->getMock();
        $connectionMock
            ->method('setFetchMode')
            ->will($this->returnValue(1));
        $connectionMock
            ->method('table')
            ->will($this->returnValue($queryMock));
        $dbServiceHandler = new DBServiceHandler($factory, $this->optimus, $connectionMock);
        $this->assertInstanceOf(ServiceHandlerEntity::class, $dbServiceHandler->findOne(1, 'slug', 'email'));
        $this->assertSame(
            $this->getToArray(),
            $dbServiceHandler->findOne(1, 'slug', 'email')->toArray()
        );

    }

    public function testFindAllFromServiceEmpty() {
        $factory = new Entity($this->optimus);
        $factory->create('ServiceHandler', []);
        $queryMock = $this->getMockBuilder(Builder::class)
            ->disableOriginalConstructor()
            ->setMethods(['where', 'get'])
            ->getMock();
        $queryMock
            ->method('where')
            ->will($this->returnValue($queryMock));
        $queryMock
            ->method('get')
            ->will($this->returnValue(new Collection([])));

        $connectionMock = $this->getMockBuilder(Connection::class)
            ->disableOriginalConstructor()
            ->setMethods(['setFetchMode', 'table'])
            ->getMock();
        $connectionMock
            ->method('setFetchMode')
            ->will($this->returnValue(1));
        $connectionMock
            ->method('table')
            ->will($this->returnValue($queryMock));
        $dbServiceHandler = new DBServiceHandler($factory, $this->optimus, $connectionMock);

        $this->assertEmpty($dbServiceHandler->findAllFromService(1, 'service'));
    }

    public function testFindAllFromService() {
        $factory = new Entity($this->optimus);
        $factory->create('ServiceHandler', []);
        $queryMock = $this->getMockBuilder(Builder::class)
            ->disableOriginalConstructor()
            ->setMethods(['where', 'get'])
            ->getMock();
        $queryMock
            ->method('where')
            ->will($this->returnValue($queryMock));
        $queryMock
            ->method('get')
            ->will($this->returnValue(new Collection([$this->getEntity(1)])));

        $connectionMock = $this->getMockBuilder(Connection::class)
            ->disableOriginalConstructor()
            ->setMethods(['setFetchMode', 'table'])
            ->getMock();
        $connectionMock
            ->method('setFetchMode')
            ->will($this->returnValue(1));
        $connectionMock
            ->method('table')
            ->will($this->returnValue($queryMock));
        $dbServiceHandler = new DBServiceHandler($factory, $this->optimus, $connectionMock);

        $this->assertInstanceOf(Collection::class, $dbServiceHandler->findAllFromService(1, 'service'));
        $this->assertSame(
            $this->getToArray(),
            $dbServiceHandler->findAllFromService(1, 'service')
                ->first()
                ->toArray()
        );
    }

    public function getAllByCompanyId() {
        $factory = new Entity($this->optimus);
        $factory->create('ServiceHandler', []);
        $queryMock = $this->getMockBuilder(Builder::class)
            ->disableOriginalConstructor()
            ->setMethods(['where', 'get'])
            ->getMock();
        $queryMock
            ->method('where')
            ->will($this->returnValue($queryMock));
        $queryMock
            ->method('get')
            ->will($this->returnValue(new Collection([$this->getEntity(1)])));

        $connectionMock = $this->getMockBuilder(Connection::class)
            ->disableOriginalConstructor()
            ->setMethods(['setFetchMode', 'table'])
            ->getMock();
        $connectionMock
            ->method('setFetchMode')
            ->will($this->returnValue(1));
        $connectionMock
            ->method('table')
            ->will($this->returnValue($queryMock));
        $dbServiceHandler = new DBServiceHandler($factory, $this->optimus, $connectionMock);

        $this->assertInstanceOf(Collection::class, $dbServiceHandler->findAllFromService(1, 'service'));
        $this->assertSame(
            $this->getToArray(),
            $dbServiceHandler->getAllByCompanyId(1)
                ->first()
                ->toArray()
        );
    }

    public function testDeleteOne() {
        $factory = new Entity($this->optimus);
        $factory->create('ServiceHandler', []);
        $queryMock = $this->getMockBuilder(Builder::class)
            ->disableOriginalConstructor()
            ->setMethods(['where', 'delete'])
            ->getMock();
        $queryMock
            ->method('where')
            ->will($this->returnValue($queryMock));
        $queryMock
            ->method('delete')
            ->will($this->returnValue(1));

        $connectionMock = $this->getMockBuilder(Connection::class)
            ->disableOriginalConstructor()
            ->setMethods(['setFetchMode', 'table'])
            ->getMock();
        $connectionMock
            ->method('setFetchMode')
            ->will($this->returnValue(1));
        $connectionMock
            ->method('table')
            ->will($this->returnValue($queryMock));
        $dbServiceHandler = new DBServiceHandler($factory, $this->optimus, $connectionMock);

        $this->assertEquals(1, $dbServiceHandler->deleteOne(1, 'service', 'service-slug'));
    }

    public function testDeleteByCompanyId() {
        $factory = new Entity($this->optimus);
        $factory->create('ServiceHandler', []);
        $queryMock = $this->getMockBuilder(Builder::class)
            ->disableOriginalConstructor()
            ->setMethods(['where', 'delete'])
            ->getMock();
        $queryMock
            ->method('where')
            ->will($this->returnValue($queryMock));
        $queryMock
            ->method('delete')
            ->will($this->returnValue(10));

        $connectionMock = $this->getMockBuilder(Connection::class)
            ->disableOriginalConstructor()
            ->setMethods(['setFetchMode', 'table'])
            ->getMock();
        $connectionMock
            ->method('setFetchMode')
            ->will($this->returnValue(1));
        $connectionMock
            ->method('table')
            ->will($this->returnValue($queryMock));
        $dbServiceHandler = new DBServiceHandler($factory, $this->optimus, $connectionMock);

        $this->assertEquals(10, $dbServiceHandler->deleteByCompanyId(1));
    }
}
