<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

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
    private $created_at;
    private $updated_at;

    public function setUp() {
        $this->optimus = $this->getMockBuilder(Optimus::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->created_at = time();
        $this->updated_at = time();
    }

    private function getEntity($id = null) {
        return new ServiceHandlerEntity(
            [
              'listens'            => ['listen1', 'listen2'],
              'id'                 => $id,
              'service.id'         => $id,
              'service.name'       => 'name',
              'service.url'        => 'url',
              'service.access'     => 'access',
              'service.enabled'    => 'enabled',
              'service.public'     => 'publicKey',
              'service.listens'    => ['listen1', 'listen2'],
              'service.triggers'   => ['trigger1', 'trigger2'],
              'service.created_at' => $this->created_at,
              'service.updated_at' => $this->updated_at,
              'service.name'       => 'name',
              'created_at'         => $this->created_at,
              'updated_at'         => $this->updated_at
            ],
            $this->optimus
        );
    }

    private function getToArray() {
        return [
            'id'      => null,
            'listens' => ['listen1', 'listen2'],
            'service' => [
                'id'         => null,
                'name'       => 'name',
                'url'        => 'url',
                'access'     => 'access',
                'enabled'    => 'enabled',
                'public'     => 'publicKey',
                'listens'    => ['listen1', 'listen2'],
                'triggers'   => ['trigger1', 'trigger2'],
                'created_at' => time(),
                'updated_at' => time(),
            ],
            'created_at' => $this->created_at,
            'updated_at' => $this->created_at
        ];
    }

    public function testFindOneNotFound() {
        $queryMock = $this->getMockBuilder(Builder::class)
            ->disableOriginalConstructor()
            ->setMethods(['where', 'get', 'join', 'first'])
            ->getMock();
        $queryMock
            ->method('where')
            ->will($this->returnValue($queryMock));
        $queryMock
            ->method('join')
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

        $dbServiceHandler = new DBServiceHandler(
            new Entity($this->optimus),
            $this->optimus,
            $connectionMock
        );
        $this->setExpectedException(NotFound::class);
        $dbServiceHandler->findOne(0, 1);
    }

    public function testFindOne() {
        // query mock
        $queryMock = $this->getMockBuilder(Builder::class)
            ->disableOriginalConstructor()
            ->setMethods(['where', 'join', 'get', 'first'])
            ->getMock();
        $queryMock
            ->method('where')
            ->will($this->returnValue($queryMock));
        $queryMock
            ->method('join')
            ->will($this->returnValue($queryMock));
        $queryMock
            ->method('get')
            ->will($this->returnValue([$this->getEntity()]));
        $queryMock
            ->method('first')
            ->will($this->returnValue($this->getEntity()));

        // connection mock
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

        $dbServiceHandler = new DBServiceHandler(
            new Entity($this->optimus),
            $this->optimus,
            $connectionMock
        );

        $entity = $dbServiceHandler->findOne(1, 1);

        $this->assertInstanceOf(ServiceHandlerEntity::class, $entity);
        // assertEquals: we want the array key => value combinations to be the same, but not necessarily in the same order
        $this->assertEquals($this->getToArray(), $entity->toArray());

    }

    public function getAllByCompanyId() {
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

        $dbServiceHandler = new DBServiceHandler(
            new Entity($this->optimus),
            $this->optimus,
            $connectionMock
        );

        $this->assertInstanceOf(Collection::class, $dbServiceHandler->findAllFromService(1, 'service'));
        // assertEquals: we want the array key => value combinations to be the same, but not necessarily in the same order
        $this->assertEquals(
            $this->getToArray(),
            $dbServiceHandler->getAllByCompanyId(1)
                ->first()
                ->toArray()
        );
    }

    public function testDeleteOne() {
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

        $dbServiceHandler = new DBServiceHandler(
            new Entity($this->optimus),
            $this->optimus,
            $connectionMock
        );

        $this->assertSame(1, $dbServiceHandler->deleteOne(1, 1));
    }

    public function testDeleteByCompanyId() {
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

        $dbServiceHandler = new DBServiceHandler(
            new Entity($this->optimus),
            $this->optimus,
            $connectionMock
        );

        $this->assertSame(10, $dbServiceHandler->deleteByCompanyId(1));
    }
}
