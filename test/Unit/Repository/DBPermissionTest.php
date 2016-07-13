<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace Test\Unit\Repository;

use App\Entity\Permission;
use App\Exception\NotFound;
use App\Factory\Entity;
use App\Repository\DBPermission;
use Illuminate\Database\Connection;
use Test\Unit\AbstractUnit;

class DBPermissionTest extends AbstractUnit {
    public function testFindOneNotFound() {
        $factory = new Entity();
        $factory->create('Permission', []);
        $queryMock = $this->getMockBuilder(Builder::class)
            ->disableOriginalConstructor()
            ->setMethods(['where', 'first', 'get'])
            ->getMock();
        $queryMock
            ->method('where')
            ->will($this->returnValue($queryMock));
        $queryMock
            ->method('get')
            ->will($this->returnValue([]));
        $queryMock
            ->method('first')
            ->will($this->returnValue([]));
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
        $dbPermission = new DBPermission($factory, $connectionMock);
        $this->setExpectedException(NotFound::class);
        $dbPermission->findOne(1, 'notAExistingRoutName');
    }

    public function testFindOne() {
        $array = [
            'route_name'     => 'companies:listAll',
            'created_at'     => time()
        ];

        $factory = new Entity();
        $entity  = $factory->create('Permission', $array);

        $queryMock = $this->getMockBuilder(Builder::class)
            ->disableOriginalConstructor()
            ->setMethods(['where', 'first', 'get'])
            ->getMock();
        $queryMock
            ->method('where')
            ->will($this->returnValue($queryMock));
        $queryMock
            ->method('get')
            ->will($this->returnValue([$entity]));
        $queryMock
            ->method('first')
            ->will($this->returnValue($array));
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
        $dbPermission = new DBPermission($factory, $connectionMock);

        $this->assertInstanceOf(Permission::class, $dbPermission->findOne(0, 'companies:listAll'));
        $this->assertSame($array, $dbPermission->findOne(0, 'companies:listAll')->toArray());
    }

}
