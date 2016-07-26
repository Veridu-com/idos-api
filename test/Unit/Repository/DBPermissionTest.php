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
use Illuminate\Support\Collection;

class DBPermissionTest extends AbstractUnit {
    public function testFindOneNotFound() {
        $factory = new Entity();
        $factory->create('Permission', []);
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
            ->setMethods(['where', 'get'])
            ->getMock();
        $queryMock
            ->method('where')
            ->will($this->returnValue($queryMock));
        $queryMock
            ->method('get')
            ->will($this->returnValue(new Collection([$entity])));

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

        // fetches entity
        $entity = $dbPermission->findOne(0, 'companies:listAll');

        $this->assertInstanceOf(Permission::class, $entity);
        $this->assertSame($array, $entity->toArray());
    }

}
